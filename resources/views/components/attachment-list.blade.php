@props(['attachments', 'deletable' => false, 'emptyMessage' => 'Aucune pièce jointe.'])

{{-- ==================================================
     Modale de visualisation + impression, injectée UNE
     SEULE FOIS par page même si ce composant est utilisé
     plusieurs fois (@once) - "Voir" ouvre un PDF/image
     directement dans l'app (iframe) avec un vrai bouton
     "Imprimer" dédié, plutôt que de dépendre du chrome du
     navigateur dans un onglet séparé (peu fiable, parfois
     bloqué par le bloqueur de pop-ups selon le navigateur).
=================================================== --}}
@once
    <style>
        /* Centrage explicite plutôt que de compter sur le
           comportement natif de <dialog> (margin: auto) - neutralisé
           par le reset CSS de Tailwind (preflight met margin: 0
           partout), ce qui laissait la modale collée en haut à
           gauche au lieu d'être centrée. */
        #attachment-viewer[open] {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            margin: 0;
            width: min(90vw, 56rem);
            max-height: 88vh;
            display: flex;
            flex-direction: column;
        }
        #attachment-viewer::backdrop {
            background: rgba(15, 23, 42, 0.6);
        }
    </style>

    <dialog id="attachment-viewer" class="rounded-2xl border border-brand-border p-0 shadow-2xl">
        <div class="flex shrink-0 items-center justify-between gap-4 border-b border-brand-border px-5 py-4">
            <div class="flex min-w-0 items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-blue/[0.08] text-brand-blue">
                    @include('layouts.partials.icon', ['name' => 'file', 'class' => 'h-4 w-4'])
                </span>
                <div class="min-w-0">
                    <p id="attachment-viewer-title" class="truncate text-sm font-semibold text-brand-navy"></p>
                    <p id="attachment-viewer-size" class="text-xs text-slate-400"></p>
                </div>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <a id="attachment-viewer-download" href="#"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-brand-border bg-white px-3 py-1.5 text-[13px] font-medium text-brand-navy shadow-sm transition hover:border-brand-blue/40 hover:bg-slate-50">
                    @include('layouts.partials.icon', ['name' => 'arrow-left', 'class' => 'h-3.5 w-3.5 -rotate-90'])
                    Télécharger
                </a>
                <button type="button" onclick="printAttachment()"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-brand-blue px-3 py-1.5 text-[13px] font-semibold text-white shadow-sm shadow-brand-blue/30 transition hover:bg-brand-blue-dark">
                    Imprimer
                </button>
                <button type="button" onclick="document.getElementById('attachment-viewer').close()"
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-brand-navy">
                    @include('layouts.partials.icon', ['name' => 'close', 'class' => 'h-4 w-4'])
                </button>
            </div>
        </div>
        <iframe id="attachment-viewer-frame" class="h-[75vh] w-full grow bg-slate-100" title="Aperçu de la pièce jointe"></iframe>
    </dialog>

    <script>
        function openAttachmentViewer(url, name, isImage, size) {
            document.getElementById('attachment-viewer-title').textContent = name;
            document.getElementById('attachment-viewer-size').textContent = size ? (size + ' Mo') : '';
            document.getElementById('attachment-viewer-download').href = url;

            var frame = document.getElementById('attachment-viewer-frame');
            frame.dataset.fileUrl = url;

            if (isImage) {
                // Une image chargée directement en `src` d'iframe s'affiche
                // à sa taille réelle (souvent bien plus grande que la
                // fenêtre) avec des barres de défilement - on l'enrobe donc
                // dans une mini-page qui la met à l'échelle, centrée, avec
                // une présentation "carte" plutôt que posée à plat.
                frame.removeAttribute('src');
                frame.srcdoc = '<!DOCTYPE html><html><head><style>' +
                    'html,body{margin:0;height:100%;display:flex;align-items:center;justify-content:center;background:#f1f5f9;}' +
                    'img{max-width:88%;max-height:88vh;object-fit:contain;background:#fff;padding:12px;border-radius:10px;box-shadow:0 4px 24px rgba(15,23,42,0.12);}' +
                    '</style></head><body><img src="' + url + '" alt=""></body></html>';
            } else {
                frame.removeAttribute('srcdoc');
                frame.src = url;
            }

            document.getElementById('attachment-viewer').showModal();
        }

        function printAttachment() {
            var frame = document.getElementById('attachment-viewer-frame');
            // Certains navigateurs n'exposent le print() du contenu
            // d'une iframe qu'une fois son contenu réellement chargé -
            // essai direct, avec repli sur l'ouverture du fichier dans
            // un nouvel onglet si le navigateur refuse (rare, mais
            // preferable a un bouton qui ne fait rien).
            try {
                frame.contentWindow.focus();
                frame.contentWindow.print();
            } catch (e) {
                window.open(frame.dataset.fileUrl || frame.src, '_blank');
            }
        }

        // Un <dialog> natif se ferme aussi au clic sur le fond -
        // vide l'iframe à la fermeture pour ne pas garder un gros PDF
        // (ou une grosse image) chargé en mémoire une fois la modale
        // fermée.
        document.addEventListener('DOMContentLoaded', function () {
            var dialog = document.getElementById('attachment-viewer');
            if (!dialog) return;
            dialog.addEventListener('close', function () {
                var frame = document.getElementById('attachment-viewer-frame');
                frame.removeAttribute('src');
                frame.removeAttribute('srcdoc');
            });
        });
    </script>
@endonce

@if ($attachments->isEmpty())
    <x-empty-state icon="file" title="{{ $emptyMessage }}" />
@else
    <ul class="divide-y divide-brand-border">
        @foreach ($attachments as $attachment)
            @php $viewable = $attachment->isPdf() || $attachment->isImage(); @endphp
            <li class="flex items-center gap-3 px-5 py-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                    @include('layouts.partials.icon', ['name' => 'file', 'class' => 'h-4 w-4'])
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-brand-navy">{{ $attachment->original_name }}</p>
                    <p class="text-xs text-slate-400">{{ $attachment->sizeInMb() }} Mo</p>
                </div>

                @if ($viewable)
                    <button type="button"
                            onclick="openAttachmentViewer({{ \Illuminate\Support\Js::from(route('workflow.attachments.download', $attachment)) }}, {{ \Illuminate\Support\Js::from($attachment->original_name) }}, {{ \Illuminate\Support\Js::from($attachment->isImage()) }}, {{ \Illuminate\Support\Js::from($attachment->sizeInMb()) }})"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-brand-border bg-white px-3 py-1.5 text-[13px] font-medium text-brand-navy shadow-sm transition hover:border-brand-blue/40 hover:bg-slate-50">
                        @include('layouts.partials.icon', ['name' => 'file', 'class' => 'h-3.5 w-3.5'])
                        Voir / Imprimer
                    </button>
                @endif

                <a href="{{ route('workflow.attachments.download', $attachment) }}"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-brand-border bg-white px-3 py-1.5 text-[13px] font-medium text-brand-navy shadow-sm transition hover:border-brand-blue/40 hover:bg-slate-50">
                    @include('layouts.partials.icon', ['name' => 'arrow-left', 'class' => 'h-3.5 w-3.5 -rotate-90'])
                    Télécharger
                </a>

                @if ($deletable)
                    <x-confirm-form
                        :action="route('workflow.attachments.destroy', $attachment)"
                        method="DELETE"
                        confirm="Supprimer « {{ $attachment->original_name }} » ?"
                        variant="ghost" icon="trash"
                    ><span class="sr-only">Supprimer</span></x-confirm-form>
                @endif
            </li>
        @endforeach
    </ul>
@endif
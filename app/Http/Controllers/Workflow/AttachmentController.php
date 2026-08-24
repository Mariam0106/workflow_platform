<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Support\AttachmentUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * ==========================================================================
 * AttachmentController
 * ==========================================================================
 *
 * BR-51 : une Demande peut porter zéro, une ou plusieurs Pièces Jointes,
 * exclusivement via les Champs de type "Fichier" définis dans la
 * structure du Formulaire (voir SubmitRequestRequest::prepareForValidation()
 * et MyRequestController::store(), qui les matérialise en Pièce Jointe
 * au moment de l'envoi) - jamais via un ajout libre indépendant de la
 * structure du Formulaire. Tout Validateur habilité (RequestPolicy::view,
 * déjà utilisé par my-validations.show) peut ensuite consulter/télécharger
 * pour impression et signature papier - hors scope applicatif, l'app ne
 * fait que mettre le document à disposition.
 *
 * Aucune nouvelle Policy : une Pièce Jointe n'a pas de règles d'accès
 * différentes de la Request qui la porte (RequestPolicy::update pour
 * la suppression tant que la Request est en Brouillon, ::view pour la
 * consultation/téléchargement).
 *
 * Stockage sur le disque "local" (privé, jamais servi directement par
 * le serveur web) - tout accès passe par download() ci-dessous, qui
 * vérifie l'autorisation avant de streamer le fichier.
 * ==========================================================================
 */
class AttachmentController extends Controller
{
    public function download(Attachment $attachment): StreamedResponse
    {
        Gate::authorize('view', $attachment->request);

        abort_unless(
            Storage::disk(AttachmentUploader::DISK)->exists($attachment->fullPath()),
            404,
            'Ce fichier n\'est plus disponible sur le serveur.',
        );

        // PDF/image : ouvert directement dans le navigateur (le
        // Validateur peut alors l'imprimer via Ctrl+P puis le signer
        // sur papier - hors scope applicatif). Les autres formats
        // (Word/Excel) ne sont pas affichables par le navigateur, on
        // force donc un vrai téléchargement dans ce cas.
        if ($attachment->isPdf() || $attachment->isImage()) {
            return Storage::disk(AttachmentUploader::DISK)->response(
                $attachment->fullPath(),
                $attachment->original_name,
                ['Content-Disposition' => 'inline; filename="' . $attachment->original_name . '"'],
            );
        }

        return Storage::disk(AttachmentUploader::DISK)->download($attachment->fullPath(), $attachment->original_name);
    }

    public function destroy(Attachment $attachment): RedirectResponse
    {
        Gate::authorize('update', $attachment->request);

        AttachmentUploader::delete($attachment);

        return back()->with('status', 'Pièce jointe supprimée.');
    }
}

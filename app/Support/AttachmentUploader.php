<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Attachment;
use App\Models\Request as RequestModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * ==========================================================================
 * AttachmentUploader
 * ==========================================================================
 *
 * Petit point d'entrée unique pour stocker un fichier envoyé par un
 * Utilisateur comme Pièce Jointe (BR-51) d'une Request - utilisé à la
 * fois par AttachmentController (section générale "Pièces jointes") et
 * par MyRequestController (champs de formulaire dynamique de type
 * "file"). Évite de dupliquer la logique de nommage/stockage entre les
 * deux, qui doit rester strictement identique (même disque, même
 * convention de répertoire) pour que Attachment::fullPath() fonctionne
 * quelle que soit l'origine de l'upload.
 * ==========================================================================
 */
final class AttachmentUploader
{
    public const DISK = 'local';

    public static function store(UploadedFile $file, RequestModel $request, int $uploadedBy): Attachment
    {
        $directory = 'attachments/' . $request->id;
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $storedName = (string) Str::uuid() . '.' . $extension;

        $file->storeAs($directory, $storedName, self::DISK);

        return $request->attachments()->create([
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $storedName,
            'storage_path' => $directory,
            'mime_type' => (string) $file->getClientMimeType(),
            'extension' => $extension,
            'size' => $file->getSize(),
            'uploaded_by' => $uploadedBy,
        ]);
    }

    public static function delete(Attachment $attachment): void
    {
        Storage::disk(self::DISK)->delete($attachment->fullPath());
        $attachment->delete();
    }
}
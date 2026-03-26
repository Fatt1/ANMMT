<?php

namespace Tests\Feature;

use Tests\TestCase;

class PathTraversalDownloadTest extends TestCase
{
    public function test_downloads_file_inside_documents_directory(): void
    {
        $this->withoutMiddleware();

        $documentsDir = storage_path('documents');

        if (!is_dir($documentsDir)) {
            mkdir($documentsDir, 0777, true);
        }

        $normalFile = $documentsDir . DIRECTORY_SEPARATOR . 'lab-note.txt';
        file_put_contents($normalFile, 'normal document');

        $response = $this->get('/download/file?file=lab-note.txt');

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=lab-note.txt');
    }

    public function test_path_traversal_reads_file_outside_documents_directory(): void
    {
        $this->withoutMiddleware();

        $privateDir = storage_path('app/private');

        if (!is_dir($privateDir)) {
            mkdir($privateDir, 0777, true);
        }

        $secretFile = $privateDir . DIRECTORY_SEPARATOR . 'secret.txt';
        file_put_contents($secretFile, 'top-secret-from-private');

        // Traversal escapes storage/documents and reaches storage/app/private.
        $response = $this->get('/download/file?file=../app/private/secret.txt');

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=secret.txt');
    }

    public function test_secure_download_allows_file_inside_documents_directory(): void
    {
        $this->withoutMiddleware();

        $documentsDir = storage_path('documents');

        if (!is_dir($documentsDir)) {
            mkdir($documentsDir, 0777, true);
        }

        $normalFile = $documentsDir . DIRECTORY_SEPARATOR . 'secure-note.txt';
        file_put_contents($normalFile, 'secure document');

        $response = $this->get('/download/file/secure?file=secure-note.txt');

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=secure-note.txt');
    }

    public function test_secure_download_blocks_path_traversal(): void
    {
        $this->withoutMiddleware();

        $privateDir = storage_path('app/private');

        if (!is_dir($privateDir)) {
            mkdir($privateDir, 0777, true);
        }

        $secretFile = $privateDir . DIRECTORY_SEPARATOR . 'blocked-secret.txt';
        file_put_contents($secretFile, 'must-not-be-readable');

        $response = $this->get('/download/file/secure?file=../app/private/blocked-secret.txt');

        $response->assertStatus(403);
    }
}

<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OrthancService
{
    private string $url;

    private ?string $username;

    private ?string $password;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {
        $url = config('services.orthanc.url');

        if (empty($url)) {
            throw new RuntimeException(
                'ORTHANC_URL belum dikonfigurasi.'
            );
        }

        $this->url = rtrim($url, '/');

        $this->username =
            config('services.orthanc.username');

        $this->password =
            config('services.orthanc.password');
    }


    /*
    |--------------------------------------------------------------------------
    | HTTP CLIENT
    |--------------------------------------------------------------------------
    */

    private function client(): PendingRequest
    {
        $client = Http::acceptJson()
            ->connectTimeout(10)
            ->timeout(600)
            ->retry(
                2,
                500,
                throw: false
            );

        /*
        |--------------------------------------------------------------------------
        | BASIC AUTH ORTHANC
        |--------------------------------------------------------------------------
        */

        if (
            !empty($this->username) &&
            $this->password !== null
        ) {
            $client = $client->withBasicAuth(
                $this->username,
                $this->password
            );
        }

        return $client;
    }


    /*
    |--------------------------------------------------------------------------
    | INFORMASI SERVER ORTHANC
    |--------------------------------------------------------------------------
    */

    public function systemInfo(): array
    {
        $response = $this->client()
            ->get(
                $this->url . '/system'
            );

        $response->throw();

        $data = $response->json();

        if (!is_array($data)) {
            throw new RuntimeException(
                'Response /system dari Orthanc tidak valid.'
            );
        }

        return $data;
    }


    /*
    |--------------------------------------------------------------------------
    | DAFTAR STUDY
    |--------------------------------------------------------------------------
    */

    public function studies(): array
    {
        $response = $this->client()
            ->get(
                $this->url . '/studies'
            );

        $response->throw();

        $data = $response->json();

        if (!is_array($data)) {
            return [];
        }

        return $data;
    }


    /*
    |--------------------------------------------------------------------------
    | DETAIL STUDY
    |--------------------------------------------------------------------------
    */

    public function study(string $studyId): array
    {
        if (trim($studyId) === '') {
            throw new RuntimeException(
                'Orthanc Study ID kosong.'
            );
        }

        $response = $this->client()
            ->get(
                $this->url .
                '/studies/' .
                rawurlencode($studyId)
            );

        $response->throw();

        $data = $response->json();

        if (!is_array($data)) {
            throw new RuntimeException(
                'Response Study dari Orthanc tidak valid.'
            );
        }

        return $data;
    }


    /*
    |--------------------------------------------------------------------------
    | DETAIL SERIES
    |--------------------------------------------------------------------------
    */

    public function series(string $seriesId): array
    {
        if (trim($seriesId) === '') {
            throw new RuntimeException(
                'Orthanc Series ID kosong.'
            );
        }

        $response = $this->client()
            ->get(
                $this->url .
                '/series/' .
                rawurlencode($seriesId)
            );

        $response->throw();

        $data = $response->json();

        if (!is_array($data)) {
            throw new RuntimeException(
                'Response Series dari Orthanc tidak valid.'
            );
        }

        return $data;
    }


    /*
    |--------------------------------------------------------------------------
    | DETAIL INSTANCE
    |--------------------------------------------------------------------------
    */

    public function instance(string $instanceId): array
    {
        if (trim($instanceId) === '') {
            throw new RuntimeException(
                'Orthanc Instance ID kosong.'
            );
        }

        $response = $this->client()
            ->get(
                $this->url .
                '/instances/' .
                rawurlencode($instanceId)
            );

        $response->throw();

        $data = $response->json();

        if (!is_array($data)) {
            throw new RuntimeException(
                'Response Instance dari Orthanc tidak valid.'
            );
        }

        return $data;
    }


    /*
    |--------------------------------------------------------------------------
    | UPLOAD DICOM
    |--------------------------------------------------------------------------
    |
    | Mendukung:
    |
    | - .dcm
    | - file DICOM tanpa ekstensi
    | - Philips Allura XA
    | - Multi-frame / Cine
    | - ZIP DICOM
    |
    */

    public function uploadDicom(
        string $filePath
    ): array {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI FILE
        |--------------------------------------------------------------------------
        */

        if (trim($filePath) === '') {
            throw new RuntimeException(
                'Path file DICOM kosong.'
            );
        }

        if (!file_exists($filePath)) {
            throw new RuntimeException(
                'File DICOM yang akan dikirim ke Orthanc tidak ditemukan.'
            );
        }

        if (!is_file($filePath)) {
            throw new RuntimeException(
                'Path DICOM bukan sebuah file.'
            );
        }

        if (!is_readable($filePath)) {
            throw new RuntimeException(
                'File DICOM tidak dapat dibaca oleh PHP.'
            );
        }

        clearstatcache(true, $filePath);

        $size = filesize($filePath);

        if ($size === false || $size <= 0) {
            throw new RuntimeException(
                'File DICOM kosong atau ukuran file tidak dapat dibaca.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BUKA FILE SEBAGAI STREAM
        |--------------------------------------------------------------------------
        |
        | Jangan membaca file dengan file_get_contents().
        |
        | Cine XA Philips bisa besar sehingga lebih aman menggunakan stream.
        |
        */

        $stream = fopen(
            $filePath,
            'rb'
        );

        if ($stream === false) {
            throw new RuntimeException(
                'File DICOM gagal dibuka untuk proses upload.'
            );
        }


        try {
            /*
            |--------------------------------------------------------------------------
            | POST /instances
            |--------------------------------------------------------------------------
            */

            $response = $this->client()
                ->withHeaders([
                    'Content-Type' =>
                        'application/octet-stream',

                    'Accept' =>
                        'application/json',
                ])
                ->send(
                    'POST',
                    $this->url . '/instances',
                    [
                        'body' => $stream,
                    ]
                );


            /*
            |--------------------------------------------------------------------------
            | ERROR DARI ORTHANC
            |--------------------------------------------------------------------------
            */

            if (!$response->successful()) {
                $body = trim(
                    $response->body()
                );

                throw new RuntimeException(
                    'Orthanc menolak file DICOM. HTTP ' .
                    $response->status() .
                    (
                        $body !== ''
                            ? ' - ' . $body
                            : ''
                    )
                );
            }


            /*
            |--------------------------------------------------------------------------
            | PARSE RESPONSE JSON
            |--------------------------------------------------------------------------
            */

            $data = $response->json();

            if (!is_array($data)) {
                throw new RuntimeException(
                    'Upload berhasil dikirim, tetapi response Orthanc tidak valid.'
                );
            }

            return $data;

        } finally {
            /*
            |--------------------------------------------------------------------------
            | TUTUP STREAM
            |--------------------------------------------------------------------------
            */

            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS STUDY
    |--------------------------------------------------------------------------
    */

    public function deleteStudy(
        string $studyId
    ): void {
        if (trim($studyId) === '') {
            throw new RuntimeException(
                'Orthanc Study ID kosong.'
            );
        }

        $response = $this->client()
            ->delete(
                $this->url .
                '/studies/' .
                rawurlencode($studyId)
            );


        /*
        |--------------------------------------------------------------------------
        | 404
        |--------------------------------------------------------------------------
        |
        | Kalau sudah tidak ada di Orthanc,
        | Laravel boleh tetap membersihkan record lokal.
        |
        */

        if ($response->status() === 404) {
            return;
        }

        $response->throw();
    }


    /*
    |--------------------------------------------------------------------------
    | CEK STUDY ADA ATAU TIDAK
    |--------------------------------------------------------------------------
    */

    public function studyExists(
        string $studyId
    ): bool {
        if (trim($studyId) === '') {
            return false;
        }

        $response = $this->client()
            ->get(
                $this->url .
                '/studies/' .
                rawurlencode($studyId)
            );

        if ($response->status() === 404) {
            return false;
        }

        $response->throw();

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | URL INTERNAL ORTHANC
    |--------------------------------------------------------------------------
    */

    public function getBaseUrl(): string
    {
        return $this->url;
    }
}
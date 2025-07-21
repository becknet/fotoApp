<?php

declare(strict_types=1);

namespace App\Controllers;

use App\FileUpload;
use App\ImageProcessor;
use App\Models\Photo;
use App\Session;

class PhotoController extends Controller
{
    private Photo $photoModel;
    private array $config;

    public function __construct()
    {
        $this->photoModel = new Photo();
        $this->config = require __DIR__ . '/../../config/app.php';
    }

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $photos = $this->photoModel->getAllPhotos($limit, $offset);
        $hasMorePhotos = count($photos) === $limit;

        $this->view('photos.index', [
            'title' => 'Photo Gallery - FotoApp',
            'photos' => $photos,
            'currentPage' => $page,
            'hasMorePhotos' => $hasMorePhotos,
        ]);
    }

    public function show(string $id): void
    {
        $photo = $this->photoModel->findByIdWithUser((int) $id);

        if (!$photo) {
            http_response_code(404);
            $this->view('errors.404', ['title' => 'Photo Not Found']);

            return;
        }

        $this->view('photos.show', [
            'title' => $photo['title'] . ' - FotoApp',
            'photo' => $photo,
            'isOwner' => Session::get('user_id') === (int) $photo['user_id'],
        ]);
    }

    public function create(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'You must be logged in to upload photos.');
            $this->redirect('/login');

            return;
        }

        $this->view('photos.create', [
            'title' => 'Upload Photo - FotoApp',
        ]);
    }

    public function store(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'You must be logged in to upload photos.');
            $this->redirect('/login');

            return;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '') ?: null;
        $location = trim($_POST['location'] ?? '') ?: null;

        $errors = $this->validate([
            'title' => 'required|min:1|max:140',
            'description' => 'max:1000',
            'location' => 'max:140',
        ], $_POST);

        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
            $errors['photo'] = 'Please select a photo to upload.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old_title', $title);
            Session::flash('old_description', $description);
            Session::flash('old_location', $location);
            $this->back();

            return;
        }

        try {
            $uploadResult = $this->uploadPhoto($_FILES['photo']);
            if (!$uploadResult) {
                Session::flash('error', 'Failed to upload photo. Please try again.');
                $this->back();

                return;
            }

            $photoId = $this->photoModel->create(
                Session::get('user_id'),
                $title,
                $description,
                $location,
                $uploadResult['photo_path'],
                $uploadResult['thumb_path']
            );

            Session::flash('success', 'Photo uploaded successfully!');
            $this->redirect("/photos/{$photoId}");
        } catch (\Exception $e) {
            Session::flash('error', 'An error occurred while uploading the photo.');
            $this->back();
        }
    }

    public function edit(string $id): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'You must be logged in to edit photos.');
            $this->redirect('/login');

            return;
        }

        $photo = $this->photoModel->findById((int) $id);

        if (!$photo) {
            http_response_code(404);
            $this->view('errors.404', ['title' => 'Photo Not Found']);

            return;
        }

        if ((int) $photo['user_id'] !== Session::get('user_id')) {
            Session::flash('error', 'You can only edit your own photos.');
            $this->redirect('/photos/' . $id);

            return;
        }

        $this->view('photos.edit', [
            'title' => 'Edit Photo - FotoApp',
            'photo' => $photo,
        ]);
    }

    public function update(string $id): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'You must be logged in to edit photos.');
            $this->redirect('/login');

            return;
        }

        $photo = $this->photoModel->findById((int) $id);

        if (!$photo) {
            http_response_code(404);
            $this->view('errors.404', ['title' => 'Photo Not Found']);

            return;
        }

        if ((int) $photo['user_id'] !== Session::get('user_id')) {
            Session::flash('error', 'You can only edit your own photos.');
            $this->redirect('/photos/' . $id);

            return;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '') ?: null;
        $location = trim($_POST['location'] ?? '') ?: null;

        $errors = $this->validate([
            'title' => 'required|min:1|max:140',
            'description' => 'max:1000',
            'location' => 'max:140',
        ], $_POST);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            $this->back();

            return;
        }

        $success = $this->photoModel->updatePhoto((int) $id, $title, $description, $location);

        if ($success) {
            Session::flash('success', 'Photo updated successfully!');
        } else {
            Session::flash('error', 'Failed to update photo.');
        }

        $this->redirect('/photos/' . $id);
    }

    public function destroy(string $id): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'You must be logged in to delete photos.');
            $this->redirect('/login');

            return;
        }

        $photo = $this->photoModel->findById((int) $id);

        if (!$photo) {
            Session::flash('error', 'Photo not found.');
            $this->redirect('/');

            return;
        }

        if ((int) $photo['user_id'] !== Session::get('user_id')) {
            Session::flash('error', 'You can only delete your own photos.');
            $this->redirect('/photos/' . $id);

            return;
        }

        $this->deletePhotoFiles($photo['file_path'], $photo['thumb_path']);

        $success = $this->photoModel->deletePhoto((int) $id);

        if ($success) {
            Session::flash('success', 'Photo deleted successfully.');
        } else {
            Session::flash('error', 'Failed to delete photo.');
        }

        $this->redirect('/');
    }

    private function uploadPhoto(array $file): ?array
    {
        $fileUpload = new FileUpload($this->config['upload']);
        $uploadResult = $fileUpload->upload($file);

        if (!$uploadResult) {
            return null;
        }

        $thumbnailName = pathinfo($uploadResult['filename'], PATHINFO_FILENAME) . '_thumb.' .
                        pathinfo($uploadResult['filename'], PATHINFO_EXTENSION);

        $thumbnailPath = dirname($uploadResult['full_path']) . '/' . $thumbnailName;

        $imageProcessor = new ImageProcessor($this->config['upload']);

        try {
            $success = $imageProcessor->generateThumbnail(
                $uploadResult['full_path'],
                $thumbnailPath,
                $this->config['upload']['thumbnail_size']
            );

            if (!$success) {
                FileUpload::delete($uploadResult['full_path']);

                return null;
            }

            return [
                'photo_path' => $uploadResult['path'],
                'thumb_path' => str_replace($uploadResult['filename'], $thumbnailName, $uploadResult['path']),
            ];
        } catch (\Exception $e) {
            FileUpload::delete($uploadResult['full_path']);

            return null;
        }
    }

    private function deletePhotoFiles(string $photoPath, string $thumbPath): void
    {
        $uploadPath = $this->config['upload']['path'];
        FileUpload::delete($uploadPath . '/' . $photoPath);
        FileUpload::delete($uploadPath . '/' . $thumbPath);
    }
}
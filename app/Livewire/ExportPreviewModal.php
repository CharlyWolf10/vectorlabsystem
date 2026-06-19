<?php

namespace App\Livewire;

use Livewire\Component;

class ExportPreviewModal extends Component
{
    public $showModal = false;
    public $previewUrl = '';
    public $exportPdfUrl = '';
    public $title = 'Previsualización del Documento';

    protected $listeners = ['openExportPreview' => 'openModal'];

    public function openModal($previewUrl, $exportPdfUrl, $title = 'Previsualización del Documento')
    {
        $this->previewUrl = $previewUrl;
        $this->exportPdfUrl = $exportPdfUrl;
        $this->title = $title;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.export-preview-modal');
    }
}

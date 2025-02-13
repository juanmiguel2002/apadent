<?php

namespace App\Helpers;

class FileHelper
{
    public static function getFileIcon($extension)
    {
        $icons = [
            'jpg' => '<i class="fas fa-image text-blue-500"></i>',
            'jpeg' => '<i class="fas fa-image text-blue-500"></i>',
            'png' => '<i class="fas fa-image text-blue-500"></i>',
            'gif' => '<i class="fas fa-image text-blue-500"></i>',
            'pdf' => '<i class="fas fa-file-pdf text-red-500"></i>',
            'doc' => '<i class="fas fa-file-word text-blue-600"></i>',
            'docx' => '<i class="fas fa-file-word text-blue-600"></i>',
            'xls' => '<i class="fas fa-file-excel text-green-600"></i>',
            'xlsx' => '<i class="fas fa-file-excel text-green-600"></i>',
            'ppt' => '<i class="fas fa-file-powerpoint text-orange-500"></i>',
            'pptx' => '<i class="fas fa-file-powerpoint text-orange-500"></i>',
            'zip' => '<i class="fas fa-file-archive text-yellow-500"></i>',
            'rar' => '<i class="fas fa-file-archive text-yellow-500"></i>',
            'txt' => '<i class="fas fa-file-alt text-gray-500"></i>',
            'mp4' => '<i class="fas fa-file-video text-purple-500"></i>',
            'avi' => '<i class="fas fa-file-video text-purple-500"></i>',
            'mp3' => '<i class="fas fa-file-audio text-green-500"></i>',
            'wav' => '<i class="fas fa-file-audio text-green-500"></i>',
            'default' => '<i class="fas fa-file text-gray-400"></i>',
        ];

        return $icons[$extension] ?? $icons['default'];
    }
}

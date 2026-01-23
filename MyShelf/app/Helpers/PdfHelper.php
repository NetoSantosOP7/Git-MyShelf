<?php

use Smalot\PdfParser\Parser;

class PdfHelper {
    
    public static function extrairInfos($caminhoArquivo) {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($caminhoArquivo);
            
            $metadata = $pdf->getDetails();
            
            return [
                'titulo' => $metadata['Title'] ?? '',
                'autor' => $metadata['Author'] ?? '',
                'total_paginas' => count($pdf->getPages()),
                'tamanho_mb' => round(filesize($caminhoArquivo) / 1024 / 1024, 2)
            ];
            
        } catch (Exception $e) {
            return [
                'titulo' => '',
                'autor' => '',
                'total_paginas' => 0,
                'tamanho_mb' => round(filesize($caminhoArquivo) / 1024 / 1024, 2)
            ];
        }
    }
    
    public static function gerarCapa($titulo, $caminhoDestino, $cor = null) {
        $largura = 300;
        $altura = 400;
        
        $imagem = imagecreatetruecolor($largura, $altura);
        
        if ($imagem === false) {
            return false;
        }
        
        if ($cor) {
            $cor = str_replace('#', '', $cor);
            $r = hexdec(substr($cor, 0, 2));
            $g = hexdec(substr($cor, 2, 2));
            $b = hexdec(substr($cor, 4, 2));
            $corFundo = imagecolorallocate($imagem, $r, $g, $b);
        } else {
            $corFundo = imagecolorallocate($imagem, 59, 130, 246);
        }
        
        $corTexto = imagecolorallocate($imagem, 255, 255, 255);
        
        if ($corFundo === false || $corTexto === false) {
            return false;
        }
        
        imagefilledrectangle($imagem, 0, 0, $largura, $altura, $corFundo);
        
        imagestring($imagem, 5, (int)($largura / 2 - 20), 40, 'PDF', $corTexto);
        
        $corBorda = imagecolorallocate($imagem, 255, 255, 255);
        imagerectangle($imagem, 20, 20, $largura - 20, $altura - 20, $corBorda);
        imagerectangle($imagem, 22, 22, $largura - 22, $altura - 22, $corBorda);
        
        $titulo = mb_substr($titulo, 0, 50);
        $palavras = explode(' ', $titulo);
        $linhas = [];
        $linhaAtual = '';
        
        foreach ($palavras as $palavra) {
            if (mb_strlen($linhaAtual . ' ' . $palavra) <= 18) {
                $linhaAtual .= ($linhaAtual ? ' ' : '') . $palavra;
            } else {
                if ($linhaAtual) {
                    $linhas[] = $linhaAtual;
                }
                $linhaAtual = $palavra;
            }
        }
        if ($linhaAtual) {
            $linhas[] = $linhaAtual;
        }
        
        $linhas = array_slice($linhas, 0, 4);
        
        $y = ($altura / 2) - (count($linhas) * 10);
        foreach ($linhas as $linha) {
            $x = ($largura - (mb_strlen($linha) * 9)) / 2;
            imagestring($imagem, 5, (int)$x, (int)$y, $linha, $corTexto);
            $y += 25;
        }
        
        $resultado = imagejpeg($imagem, $caminhoDestino, 90);
        
        return $resultado;
    }


    
}
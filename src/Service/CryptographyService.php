<?php

namespace App\Service;



class CryptographyService
{
    const DIMENSION = 256;
    const MATRIX_SIZE = 3;


    public function encryptMessage($mess, $keyMatrix)
    {
        $matrixMes = [];
        $mess = iconv('UTF8', 'CP1251', $mess);

        $mess = str_split($mess, 1);

        foreach ($mess as $symbol){
            $matrixMes[] = ord($symbol);
        }

        while (count($matrixMes)%self::MATRIX_SIZE != 0){
            $matrixMes[] = 0;
        }
        $matrixMes = array_chunk($matrixMes, self::MATRIX_SIZE);
        $resultMatrix = [];
        foreach ($matrixMes as $key => $val){
            $val = array_chunk($val, 1);
            $resultMatrix[] = $this->getMatrixMultiplication($keyMatrix, $val);
        }
        $resultMatrixInLine = [];
        foreach ($resultMatrix as $key => $block) {
            foreach ($block as $blockKey => $value){
                $resultMatrixInLine[] = $value;
            }
        }
        return $resultMatrixInLine;
    }

    public function decodeMessage($mess, $keyMatrix)
    {
        $keyMatrix = $this->getInverseMatrix($keyMatrix);

        /*for ($i = 0; $i < count($mess); $i++){
            $mess[$i] = ord($mess[$i]);
        }*/
        $mess = array_chunk($mess, self::MATRIX_SIZE);
        $resultMatrix = [];
        foreach ($mess as $key => $val){
            $val = array_chunk($val, 1);
            $resultMatrix[] = $this->getMatrixMultiplication($keyMatrix, $val);
        }
        $resultMatrixInLine = [];
        foreach ($resultMatrix as $key => $block) {
            foreach ($block as $blockKey => $value){
                $resultMatrixInLine[] = iconv('CP1251', 'UTF8', chr($value));
            }
        }
        return $resultMatrixInLine;
    }

    public function getMatrix()
    {
        $matrix = [];
        for ($i = 0; $i < self::MATRIX_SIZE; $i++){
            for ($j = 0; $j < self::MATRIX_SIZE; $j++){
                $matrix[$i][$j] = random_int(0, self::DIMENSION);
            }
        }
        $det = $this->getDeterminant($matrix);
        $reversDet = $this->getReverseNumber($det);
        if ($reversDet != null){
            return $matrix;
        }
        return $this->getMatrix();
    }

    public function getDeterminant($matrix)
    {
        $width = count($matrix);
        $det = null;

        if ($width === 1){
            $det = $matrix[0][0];
        }
        elseif ($width === 2) {
            $det = $matrix[0][0]*$matrix[1][1]-$matrix[0][1]*$matrix[1][0];
        }
        elseif ($width > 2) {
            for ($k=0; $k < $width; $k++){
                $minor = [];
                for ($i = 0; $i < $width; $i++) {
                    for ($j = 0; $j < $width; $j++) {
                        if ($i != 0 && $j != $k) {
                            $minor[] = $matrix[$i][$j];
                        }
                    }
                }
                $minor = array_chunk($minor, $width-1);
                $minor = $this->getDeterminant($minor);
                $det += $matrix[0][$k]*$minor*(-1)**(2+$k);
            }
        }
        return $this->getMod($det);
    }

    public function getMod($val)
    {
        if ($val < 0) {
            $val = self::DIMENSION + ($val % self::DIMENSION);
        }else {
            $val = $val % self::DIMENSION;
        }
        return $val;
    }

    public function getReverseNumber($number, $z=1)
    {
        if (gmp_gcd($number, self::DIMENSION) == 1){
            $revers = $z/$number;
            if (is_int($revers)){
                if ($this->getMod($revers * $number) === 1){
                    return $revers;
                }
            }
            return $this->getReverseNumber($number, $z + self::DIMENSION);
        }
        return null;
    }

    public function getInverseMatrix($matrix){
        $det = $this->getDeterminant($matrix);
        $reversDet = $this->getReverseNumber($det);
        $width = count($matrix);
        $cMatrix = [];
        $inverseMatrix = [];
        for ($m=0; $m<$width; $m++){
            for ($n=0; $n<$width; $n++) {
                $minor = [];
                for ($i = 0; $i < $width; $i++) {
                    for ($j = 0; $j < $width; $j++) {
                        if ($i != $m && $j != $n) {
                            $minor[] = $matrix[$i][$j];
                        }
                    }
                }
                $minor = array_chunk($minor, $width-1);
                $cMatrix[] = $this->getDeterminant($minor);
            }
        }
        $cMatrix = array_chunk($cMatrix, $width);
        for ($i = 0; $i<$width; $i++){
            for ($j = 0; $j<$width; $j++){
                $cMatrix[$i][$j] =  $cMatrix[$i][$j] * (-1)**($i+1+$j+1);
                $inverseMatrix[$j][$i] = $this->getMod($cMatrix[$i][$j] * $reversDet);
            }
        }
        return $inverseMatrix;
    }

    public function getMatrixMultiplication($matrix1, $matrix2){
        $widthM1 = count($matrix1);
        $widthM2 = count($matrix2);
        $widthRowM2 = count($matrix2[0]);
        $result = [];
        for ($i = 0; $i < $widthM1; $i++){
            for($j = 0; $j < $widthRowM2; $j++){
                $result[$i][$j]=0;
                for($k = 0; $k < $widthM2; $k++){
                    $result[$i][$j]+=$matrix1[$i][$k]*$matrix2[$k][$j];
                }
            }
        }
        $resultMod = [];
        foreach ($result as $key => $value){
            $resultMod[] = $this->getMod($value[0]);
        }

        return $resultMod;
    }
}
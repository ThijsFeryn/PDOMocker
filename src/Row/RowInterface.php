<?php
namespace PDOMocker\Row;
interface RowInterface {
    public function isVisible();
    public function setVisible($visible=true);
    public function getRow();
}
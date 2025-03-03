<?php

class Image {
  private $src;
  private $alt;
  private $title;
  private $subtitle;

  public function __construct($src, $alt, $title = "", $subtitle = "") {
    $this->src = $src;
    $this->alt = $alt;
    $this->title = ($title == "") ? $this->alt : $title;
    $this->subtitle = ($subtitle == "") ? $this->title : $subtitle;
  }

  public function getImage() {
    return ['src' => $this->src, 'alt' => $this->alt, 'title' => $this->title];
  }

  public function getSubtitle() {
    return $this->subtitle;
  }
}

?>

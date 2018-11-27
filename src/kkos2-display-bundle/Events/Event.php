<?php

namespace Kkos2\KkOs2DisplayIntegrationBundle\Events;

class Event
{
  private $nid;
  private $billede;
  private $kortbeskrivelse;
  private $sted;
  private $overskrift;
  private $langbeskrivelse;
  private $startdato = '';
  private $slutdato = '';
  private $tid = '';

  /**
   * @return mixed
   */
  public function getStartdato()
  {
    return $this->startdato;
  }

  /**
   * @param mixed $startdatoArr
   */
  public function setStartdato($startdatoArr)
  {
    if (!empty($startdatoArr['item'])) {
      $this->startdato = $startdatoArr['item'];
    }
  }

  /**
   * @return mixed
   */
  public function getSlutdato()
  {
    return $this->slutdato;
  }

  /**
   * @param mixed $slutdatoArr
   */
  public function setSlutdato($slutdatoArr)
  {
    if (!empty($slutdatoArr['item'])) {
      $this->slutdato = $slutdatoArr['item'];
    }
  }

  /**
   * @return mixed
   */
  public function getTid()
  {
    return $this->tid;
  }

  /**
   * @param mixed $tidArr
   */
  public function setTid($tidArr)
  {
    if (!empty($tidArr['item'])) {
      $this->tid = $tidArr['item'];
    }
  }


  /**
   * @return mixed
   */
  public function getNid()
  {
    return $this->nid;
  }

  /**
   * @param mixed $nid
   */
  public function setNid($nid)
  {
    $this->nid = $nid;
  }

  /**
   * @return mixed
   */
  public function getBillede()
  {
    return $this->billede;
  }

  /**
   * @param mixed $billede
   */
  public function setBillede($billede)
  {
    $this->billede = $billede;
  }

  /**
   * @return mixed
   */
  public function getKortbeskrivelse()
  {
    return $this->kortbeskrivelse;
  }

  /**
   * @param mixed $kortbeskrivelse
   */
  public function setKortbeskrivelse($kortbeskrivelse)
  {
    $this->kortbeskrivelse = $kortbeskrivelse;
  }

  /**
   * @return mixed
   */
  public function getSted()
  {
    return $this->sted;
  }

  /**
   * @param mixed $sted
   */
  public function setSted($sted)
  {
    $this->sted = $sted;
  }

  /**
   * @return mixed
   */
  public function getOverskrift()
  {
    return $this->overskrift;
  }

  /**
   * @param mixed $overskrift
   */
  public function setOverskrift($overskrift)
  {
    $this->overskrift = $overskrift;
  }

  /**
   * @return mixed
   */
  public function getLangbeskrivelse()
  {
    return $this->langbeskrivelse;
  }

  /**
   * @param mixed $langbeskrivelse
   */
  public function setLangbeskrivelse($langbeskrivelse)
  {
    $this->langbeskrivelse = $langbeskrivelse;
  }

  public function isCurrent()
  {
    $start = \DateTime::createFromFormat('d.m.Y', $this->startdato);
    $slut = \DateTime::createFromFormat('d.m.Y', $this->slutdato);
    $now = new \DateTime();
    if ($start < $now && $slut < $now) {
      return FALSE;
    }
    return TRUE;
  }
}

<!DOCTYPE html>
<html>
  <?php include 'inc/head.inc'; ?>
  <header class="header" role="banner">
    <div class="header--inner">
      <?php include 'inc/header-main.inc'; ?>
    </div>
    <?php include 'inc/header-sub.inc'; ?>
  </header>
  <div class="layout-wrapper">
    <section id="create-slide-two" class="create-slide--section">
      <div class="create-slide--content--slide-two">
        <h1 class="create-slide--header">Opret slide (2/4)</h1>
        <p class="create-slide--header-description">Vælg hvilken type skærm du har - er den bred eller høj?</p>
        <div class="create-slide--screens">
          <div class="create-slide--screen-landscape">
            <a href="#" class="create-slide--screen-link is-selected">
              <span class="create-slide--overlay">
                <span class="create-slide--link-check"></span>
              </span>
              <img src="/images/outlines/landscape-outline.png"/>
            </a>
          </div>
          <div class="create-slide--screen-portrait">
            <a href="#" class="create-slide--screen-link">
              <span class="create-slide--overlay">
                <span class="create-slide--link-check"></span>
              </span>
              <img src="/images/outlines/portrait-outline.png" />
            </a>
          </div>
        </div>
        <input type="submit" class="create-slide--button" value="Gem og fortsæt">
      </div>
    </section>
  </div>
</html>
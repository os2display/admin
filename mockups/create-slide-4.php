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
    <section id="create-slide-four" class="create-slide--section">
      <div class="create-slide--content--slide-four">
        <h1 class="create-slide--header">Opret slide (4/4)</h1>
        <p class="create-slide--header-description">Tilføj indhold - husk at se dit indhold på alle dine skærme.</p>
        <div class="create-slide--config" style="height:540px;width: 940px;font-size: 18px;">
          <div class="create-slide--config-text-wrapper">
            <div class="create-slide--config-text">
              <a class="create-slide--edit-box-settings" href="#"></a>
              <h1 class="slide--header">My awesome slide<a class="create-slide--edit-title" href="#"></a></h1>
              <div class="slide--description">
                This is the content of the slide, edit this text. The bounding box will expand accoding to how much text you add. Note that the maximum height of this textbox is 50% of the total height of the slide.
                <a class="create-slide--edit-description" href="#"></a>
              </div>
            </div>
          </div>
          <a class="create-slide--edit-image-settings" href="#"></a>
          <img src="/images/outlines/slide-config-default.png" class="create-slide--image"/>
        </div>
        <input type="submit" class="create-slide--button" value="Gem og opret">
      </div>
    </section>
  </div>
</html>
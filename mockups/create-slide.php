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
    <section id="create-slide-one" class="create-slide--section">
      <div class="create-slide--content--slide-one">
        <h1 class="create-slide--header">Opret slide (1/4)</h1>
        <p class="create-slide--header-description">Giv dit slide et godt navn så du kan finde det igen senere.</p>
        <input type="text" class="create-slide--text is-valid" placeholder="Skriv f.eks. Frokost uge 10, Billeder fra DHL eller lignende">
        <input type="submit" class="create-slide--button is-inactive" value="Gem og fortsæt">
      </div>
    </section>
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
    <section id="create-slide-three" class="create-slide--section">
      <div class="create-slide--content--slide-three">
        <h1 class="create-slide--header">Opret slide (3/4)</h1>
        <p class="create-slide--header-description">Vælg template - du kan altid ændre dt valg senere.</p>
        <div class="create-slide--templates">
          <div class="create-slide--template">
            <a href="#" class="create-slide--template-link">
              <span class="create-slide--overlay">
                <span class="create-slide--link-check"></span>
              </span>
              <img src="/images/outlines/template-example-1.png"/>
            </a>
          </div>
          <div class="create-slide--template">
            <a href="#" class="create-slide--template-link is-selected">
              <span class="create-slide--overlay">
                <span class="create-slide--link-check"></span>
              </span>
              <img src="/images/outlines/template-example-2.png"/>
            </a>
          </div>
          <div class="create-slide--template">
            <a href="#" class="create-slide--template-link">
              <span class="create-slide--overlay">
                <span class="create-slide--link-check"></span>
              </span>
              <img src="/images/outlines/template-example-3.png"/>
            </a>
          </div>
          <div class="create-slide--template">
            <a href="#" class="create-slide--template-link">
              <span class="create-slide--overlay">
                <span class="create-slide--link-check"></span>
              </span>
              <img src="/images/outlines/template-example-4.png"/>
            </a>
          </div>
          <div class="create-slide--template">
            <a href="#" class="create-slide--template-link">
              <span class="create-slide--overlay">
                <span class="create-slide--link-check"></span>
              </span>
              <img src="/images/outlines/template-example-5.png"/>
            </a>
          </div>
        </div>
        <input type="submit" class="create-slide--button is-inactive" value="Gem og fortsæt">
      </div>
    </section>
    <section id="create-slide-four" class="create-slide--section">
      <div class="create-slide--content--slide-four">
        <h1 class="create-slide--header">Opret slide (4/4)</h1>
        <p class="create-slide--header-description">Tilføj indhold - husk at se dit indhold på alle dine skærme.</p>
        <div class="create-slide--config">
          <div class="create-slide--config-text-wrapper">
            <a class="create-slide--edit-box-settings" href="#"></a>
            <h1 class="slide--header">My awesome slide<a class="create-slide--edit-title" href="#"></a></h1>
            <div class="slide--description">
              This is the content of the slide, edit this text. The bounding box will expand accoding to how much text you add. Note that the maximum height of this textbox is 50% of the total height of the slide.
              <a class="create-slide--edit-description" href="#"></a>
            </div>
          </div>
          <a class="create-slide--edit-image-settings" href="#"></a>
          <img src="/images/outlines/slide-config-default.png"/>
        </div>
        <input type="submit" class="create-slide--button" value="Gem og opret">
      </div>
    </section>
  </div>
</html>
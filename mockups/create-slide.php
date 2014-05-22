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
              <span class="create-slide--screen-link-check"></span>
              <img src="/images/outlines/landscape-outline.png"/>
            </a>
          </div>
          <div class="create-slide--screen-portrait">
            <a href="#" class="create-slide--screen-link">
              <span class="create-slide--screen-link-check"></span>
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
              <span class="create-slide--template-link-check"></span>
              <img src="/images/outlines/template-example-1.png"/>
            </a>
          </div>
          <div class="create-slide--template">
            <a href="#" class="create-slide--template-link is-selected">
              <span class="create-slide--template-link-check"></span>
              <img src="/images/outlines/template-example-2.png"/>
            </a>
          </div>
          <div class="create-slide--template">
            <a href="#" class="create-slide--template-link">
              <span class="create-slide--template-link-check"></span>
              <img src="/images/outlines/template-example-3.png"/>
            </a>
          </div>
          <div class="create-slide--template">
            <a href="#" class="create-slide--template-link">
              <span class="create-slide--template-link-check"></span>
              <img src="/images/outlines/template-example-4.png"/>
            </a>
          </div>
          <div class="create-slide--template">
            <a href="#" class="create-slide--template-link">
              <span class="create-slide--template-link-check"></span>
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
      </div>
    </section>
  </div>
</html>
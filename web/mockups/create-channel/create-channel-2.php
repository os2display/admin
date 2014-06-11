<!DOCTYPE html>
<html>
  <?php include '../inc/head.inc'; ?>
  <header class="header" role="banner">
    <div class="header--inner">
      <?php include '../inc/header-main.inc'; ?>
    </div>
    <?php include '../inc/header-sub.inc'; ?>
  </header>
  <div class="layout-wrapper">
    <section id="create-channel-two" class="create-channel--section">
      <div class="create-channel--content--channel-two">
        <h1 class="create-channel--heaeder">Opret kanal - vælg skærmtype (2/4)</h1>
        <p class="create-channel--header-description">Vælg hvilken type skærme du du vil vise kanalen på - er de brede eller høje?</p>
        <div class="create-channel--screens">
          <div class="create-channel--screen-landscape">
            <a href="#" class="create-channel--screen-link is-selected">
              <span class="create-channel--overlay">
                <span class="create-channel--link-check"></span>
              </span>
              <img src="/images/outlines/landscape-outline.png"/>
            </a>
          </div>
          <div class="create-channel--screen-portrait">
            <a href="#" class="create-channel--screen-link">
              <span class="create-channel--overlay">
                <span class="create-channel--link-check"></span>
              </span>
              <img src="/images/outlines/portrait-outline.png" />
            </a>
          </div>
        </div>
        <input type="submit" class="create-channel--button" value="Gem og fortsæt">
      </div>
    </section>
  </div>
</html>
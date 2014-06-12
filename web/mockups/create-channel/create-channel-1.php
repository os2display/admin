<!DOCTYPE html>
<html>
  <?php include '../inc/head.inc'; ?>
  <body>
    <a class="nav-overlay"></a>
    <?php include '../inc/nav-menu.inc'; ?>
    <header class="header" role="banner">
      <div class="header--inner">
        <?php include '../inc/header-main.inc'; ?>
      </div>
      <?php include '../inc/header-sub.inc'; ?>
    </header>
    <div class="layout-wrapper">
      <section id="create-channel-one" class="create-channel--section">
        <div class="create-channel--content--channel-one">
          <h1 class="create-channel--header">Opret kanal (1/4)</h1>
          <p class="create-channel--header-description">Giv din kanal et godt navn</p>
          <input type="text" class="create-channel--text is-valid" placeholder="F.eks. skærme i forhallen, skærm i børnesektion eller lignende">
          <input type="submit" class="create-channel--button is-inactive" value="Gem og fortsæt">
        </div>
      </section>
    </div>
  </body>
</html>
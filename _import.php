<script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="./assets/javascript/fitty.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
<link rel="apple-touch-icon" sizes="180x180" href="./apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="./favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="./favicon-16x16.png">
<link rel="shortcut icon" href="./favicon.ico">
<link rel="manifest" href="./site.webmanifest">
<link rel="mask-icon" href="./safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#2b5797">
<meta name="theme-color" content="#ffffff">
<meta property='og:image' content='http://www.vizualizacia-dveri.sk/assets/img/logo.png'/>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-69678858-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-69678858-3');
</script>

<?php

    foreach (glob('assets/css/*.css') as $filename)
    {
        echo '<link rel="stylesheet" type="text/css" href="' . $filename . '">' . "\r\n\t\t";
    }

    foreach (glob('assets/javascript/*.js') as $filename)
    {
        echo '<script src="' . $filename . '"></script>' . "\r\n\t\t";
    }

?>
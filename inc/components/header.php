<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Subject Explorer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"></script>
    <link rel="apple-touch-icon" sizes="180x180" href="/r/i/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/r/i/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/r/i/favicon-16x16.png">
    <!--script src="subject-explorer.js"></script-->
    <script type="text/javascript">
        (function(c, l, a, r, i, t, y) {
            c[a] = c[a] || function() {
                (c[a].q = c[a].q || []).push(arguments)
            };
            t = l.createElement(r);
            t.async = 1;
            t.src = "https://www.clarity.ms/tag/" + i;
            y = l.getElementsByTagName(r)[0];
            y.parentNode.insertBefore(t, y);
        })(window, document, "clarity", "script", "hnz5t9hnfz");
    </script>
    <style>
        .small-font {
            font-size: 12px;
        }
    </style>

</head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-N60XK5LN7W"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-N60XK5LN7W');
</script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-259175413-3"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-259175413-3');
</script>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">

                <a class="navbar-brand" href="/"><img src="/r/i/favicon-32x32.png" alt="<?php echo $siteName; ?>" /> <?php echo $siteName; ?> <?php if ($MyUser->isPro()) : ?>Pro<?php endif; ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <?php if ($MyUser->isLoggedIn()) : ?>
                            <li class="nav-item"><a class="nav-link" aria-current="page" href="/">Dashboard</a></li>
                        <?php endif; ?>
                    </ul>
                    <ul class="navbar-nav">
                        <?php if ($MyUser->isLoggedIn()) : ?>
                            <div class="position-relative">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php echo $MyUser->getFullName(); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/signout">Sign Out</a></li>
                                </ul>
                            </div>
                        <?php else : ?>
                            <li class="nav-item"><a href="/signin" class="nav-link">Sign In</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="bg-light p-5 mb-3">
        <div class="container">
            <h1 class="display-4"><?php echo $pageTitle; ?></h1>
            <p class="lead"><?php echo $pageDescription; ?></p>
        </div>
    </section>
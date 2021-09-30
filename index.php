<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'includes/connection.php';

// database stats
$result = mysqli_query($link,"select count(1) FROM `accounts`");
$row = mysqli_fetch_array($result);

$accs = number_format($row[0]);

$result = mysqli_query($link,"select count(1) FROM `apps`");
$row = mysqli_fetch_array($result);

$apps = number_format($row[0]);

$result = mysqli_query($link,"select count(1) FROM `keys`");
$row = mysqli_fetch_array($result);

$keys = number_format($row[0]);

mysqli_close($link);

// request stats

if(file_exists('flux.json'))
{
$json_object = file_get_contents('flux.json');
$data = json_decode($json_object, true);

$url = "https://rest.fluxcdn.com/log/stats/v2/5633/1d";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "session: " . $data['session'],
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
if($httpcode !== 200)
{
	$ch = curl_init( "https://rest.fluxcdn.com/users/login" );
	$payload = json_encode( array( "email"=> $data['email'], "password"=> $data['pass'] ) );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($result);
	$data['session'] = $json->token;
	$json_object = json_encode($data);
	file_put_contents('flux.json', $json_object);
	$num = "N/A";
}
else
{
	$user = json_decode($response);
	$num = 0;
	foreach($user->statistics as $mydata)
	
		{
				$num += $mydata->successful;
		}
	$num = number_format($num);
}

}
else
{
$num = "N/A";	
}
?>
<!--

=========================================================
* Impact Design System - v1.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/impact-design-system
* Copyright 2010 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://github.com/creativetimofficial/impact-design-system/blob/master/LICENSE.md)

* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

-->

<!DOCTYPE html>
<html lang="en">

<head> 
    <!-- Primary Meta Tags -->
<title>KeyAuth - Open Source Auth</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="title" content="KeyAuth - Open Source Auth">

<!-- Canonical SEO -->
<link rel="canonical" href="https://keyauth.com" />

<meta content="Secure your software against piracy, an issue causing $422 million in losses anually - Fair pricing & Features not seen in competitors" name="description" />
<meta content="KeyAuth" name="author" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="keywords" content="KeyAuth, Cloud Authentication, Key Authentication,Authentication, API authentication,Security, Encryption authentication, Authenticated encryption, Cybersecurity, Developer, SaaS, Software Licensing, Licensing" />
<meta property=”og:description” content="Secure your software against piracy, an issue causing $422 million in losses anually - Fair pricing & Features not seen in competitors" />
<meta property="og:image" content="https://cdn.keyauth.com/front/assets/img/favicon.png" />
<meta property=”og:site_name” content="KeyAuth | Secure your software from piracy." />

<!-- Schema.org markup for Google+ -->
<meta itemprop="name" content="KeyAuth - Open Source Auth">
<meta itemprop="description" content="Secure your software against piracy, an issue causing $422 million in losses anually - Fair pricing & Features not seen in competitors">

<meta itemprop="image" content="https://cdn.keyauth.com/front/assets/img/favicon.png">

<!-- Twitter Card data -->
<meta name="twitter:card" content="product">
<meta name="twitter:site" content="@keyauth">
<meta name="twitter:title" content="KeyAuth - Open Source Auth">

<meta name="twitter:description" content="Secure your software against piracy, an issue causing $422 million in losses anually - Fair pricing & Features not seen in competitors">
<meta name="twitter:creator" content="@keyauth">
<meta name="twitter:image" content="https://cdn.keyauth.com/front/assets/img/favicon.png">


<!-- Open Graph data -->
<meta property="og:title" content="KeyAuth - Open Source Auth" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://keyauth.com/" />

<!-- Favicon -->
<link rel="icon" type="image/png" href="https://cdn.keyauth.com/front/assets/img/favicon.png">

<!-- Fontawesome -->
<link type="text/css" href="https://cdn.keyauth.com/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">

<!-- Nucleo icons -->
<link rel="stylesheet" href="https://cdn.keyauth.com/assets/css/nucleo.css" type="text/css">

<!-- Prism -->
<link type="text/css" href="https://cdn.keyauth.com/vendor/prismjs/themes/prism.css" rel="stylesheet">

<!-- Front CSS -->
<link type="text/css" href="https://cdn.keyauth.com/front/css/front.css" rel="stylesheet">

<!-- swiper for reviews -->
<link rel="stylesheet" href="https://cdn.keyauth.com/front/css/swiper.min.css">

<!-- Anti-flicker snippet (recommended)
<style>.async-hide { opacity: 0 !important} </style>
<script>(function(a,s,y,n,c,h,i,d,e){s.className+=' '+y;h.start=1*new Date;
h.end=i=function(){s.className=s.className.replace(RegExp(' ?'+y),'')};
(a[n]=a[n]||[]).hide=h;setTimeout(function(){i();h.end=null},c);h.timeout=c;
})(window,document.documentElement,'async-hide','dataLayer',4000,
{'GTM-K9BGS8K':true});</script>

<!-- Analytics-Optimize Snippet
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-46172202-22', 'auto', {allowLinker: true});
ga('set', 'anonymizeIp', true);
ga('require', 'GTM-K9BGS8K');
ga('require', 'displayfeatures');
ga('require', 'linker');
ga('linker:autoLink', ["2checkout.com","avangate.com"]);
</script>
<!-- end Analytics-Optimize Snippet -->

<!-- Google Tag Manager
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-NKDMSK6');</script>
<!-- End Google Tag Manager -->
</head>

<body>

    <!-- Google Tag Manager (noscript)
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NKDMSK6"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <header class="header-global">
    <nav id="navbar-main" class="navbar navbar-main navbar-expand-lg headroom py-lg-3 px-lg-6 navbar-dark navbar-theme-primary">
        <div class="container">
            <div class="navbar-collapse collapse" id="navbar_global">
                <ul class="navbar-nav navbar-nav-hover justify-content-center">
                    <li class="nav-item">
                        <a href="#features" class="nav-link">Features</a>
                    </li>
					<li class="nav-item">
                        <a href="#pricing" class="nav-link">Pricing</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" aria-expanded="false" data-toggle="dropdown">
                            <span class="nav-link-inner-text mr-1">Support</span>
                            <i class="fas fa-angle-down nav-link-arrow"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg">
                            <div class="col-auto px-0" data-dropdown-content>
                                <div class="list-group list-group-flush">
                                    <a target="_blank" href="https://docs.keyauth.com"
                                        class="list-group-item list-group-item-action d-flex align-items-center p-0 py-3 px-lg-4">
                                        <span class="icon icon-sm icon-secondary"><i class="fas fa-file-alt"></i></span>
                                        <div class="ml-4">
                                            <span class="text-dark d-block">Documentation<span
                                                    class="badge badge-sm badge-secondary ml-2">v1.0</span></span>
                                            <span class="small">Examples and guides</span>
                                        </div>
                                    </a>
                                    <a target="_blank" href="https://keyauth.com/discord"
                                        class="list-group-item list-group-item-action d-flex align-items-center p-0 py-3 px-lg-4">
                                        <span class="icon icon-sm icon-primary"><i
                                                class="fas fa-microphone-alt"></i></span>
                                        <div class="ml-4">
                                            <span class="text-dark d-block">Support</span>
                                            <span class="small">Found a bug? Create an issue!</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="d-none d-lg-block d-lg-none">
                <a href="https://keyauth.com/login" class="btn btn-md btn-docs btn-outline-white animate-up-2 mr-3"><i class="fas fa-sign-in-alt mr-2"></i> Login</a>
                <a href="https://keyauth.com/register" class="btn btn-md btn-secondary animate-up-2"><i class="fas fa-paper-plane mr-2"></i> Register</a>
            </div>
            <div class="d-flex d-lg-none align-items-center">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar_global"
                    aria-controls="navbar_global" aria-expanded="false" aria-label="Toggle navigation"><span
                        class="navbar-toggler-icon"></span></button>
            </div>
        </div>
    </nav>
</header>

    <main>

        <!-- Hero -->
        <section class="section-header pb-9 pb-lg-12 bg-primary text-white">
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-12 col-sm-8 col-md-7 col-lg-6 text-center">
                        <h1 class="display-4 text-muted mb-5 font-weight-normal">KeyAuth is an Open-source authentication system with cloud-hosted subscriptions available as well.</h1>
                        <div class="d-flex align-items-center justify-content-center mb-5">
                            <a href="https://keyauth.com/register" class="btn btn-secondary mb-3 mt-2 mr-3 animate-up-2"><span class="fas fa-user-plus mr-2"></span> Register</a>
                            <div class="mt-1">
                                <!-- Place this tag where you want the button to render. -->
                                <a class="github-button" href="https://github.com/KeyAuth/KeyAuth-Source-Code" data-color-scheme="no-preference: dark; light: light; dark: light;" data-icon="octicon-star" data-size="large" data-show-count="true">Star</a>                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pattern bottom"></div>
        </section>
        <div class="section pt-0">
            <div class="container mt-n10 mt-lg-n12 z-2">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <img src="https://cdn.keyauth.com/front/assets/img/presentation-mockup.png" alt="illustration">
                    </div>
                </div>
            </div>
        </div>
        <section class="section section-lg pt-0">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2 class="display-2 text-center mb-5 mb-lg-7">Why KeyAuth?</h2>
                    </div>
                </div>
                <div class="row justify-content-between align-items-center mb-5 mb-lg-7">
                    <div class="col-lg-5 order-lg-2">
                        <h2 class="h1">End-to-end encryption</h2>
                        <p class="mb-5">KeyAuth values privacy and security. Preserve your user's experience and your application's integrity. </p>
                        <p class="lead mb-4">Protect your user's data from start to finish.</p>
                        <div class="d-flex justify-content-between align-items-center mt-lg-4 mb-4">
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <img src="https://cdn.keyauth.com/front/assets/img/presentation-mockup-2.png" alt="Front pages overview">
                    </div>
                </div>
                <div class="row justify-content-center mb-5 mb-lg-7">
                    <div class="col-6 col-md-3 text-center mb-4">
                        <div class="icon icon-shape icon-lg bg-white shadow-lg border-light rounded-circle icon-secondary mb-4">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="font-weight-bolder"><?php echo $accs; ?></h3>
                        <p class="text-gray">Accounts</p>
                    </div>
                    <div class="col-6 col-md-3 text-center mb-4">
                        <div class="icon icon-shape icon-lg bg-white shadow-lg border-light rounded-circle icon-secondary mb-4">
                            <i class="fas fa-th"></i>
                        </div>
                        <h3 class="font-weight-bolder"><?php echo $apps; ?></h3>
                        <p class="text-gray">Applications</p>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                        <div class="icon icon-shape icon-lg bg-white shadow-lg border-light rounded-circle icon-secondary mb-4">
                            <i class="fas fa-key"></i>
                        </div>
                        <h3 class="font-weight-bolder"><?php echo $keys; ?></h3>
                        <p class="text-gray">Licenses</p>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                        <div class="icon icon-shape icon-lg bg-white shadow-lg border-light rounded-circle icon-secondary mb-4">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="font-weight-bolder"><?php echo $num; ?></h3>
                        <p class="text-gray">Requests in the last 24h</p>
                    </div>
                </div>
                <div class="row justify-content-between align-items-center mb-5 mb-lg-7">
                    <div class="col-lg-5">
                        <h2 class="h1">Detailed Tutorials</h2>
                        <p class="mb-5">KeyAuth provides descriptive visual instructions for novice programmers.</p>
                        <div class="d-flex justify-content-between align-items-center mt-lg-4 mb-4">
                            <div class="d-block">
                                <a href="https://youtube.com/keyauth" target="_blank" class="btn btn-primary mr-3 animate-up-2 mb-3"><i class="fab fa-youtube mr-2"></i> YouTube Channel</a>
                                <a href="https://github.com/keyauth" target="_blank" class="btn btn-outline-gray animate-up-2 mb-3"><i class="fab fa-github mr-2"></i> GitHub</a>
                            </div>
                        </div>
                    </div>
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/uJ0Umy_C6Fg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
                </div>
            </div>
        </section>
        <section class="section section-lg bg-soft">
            <div class="swiper-container pt-5 pb-6 swiper-container-initialized swiper-container-horizontal" style="cursor: grab;">

              <div class="swiper-wrapper" style="transition-duration: 0ms; transform: translate3d(-960px, 0px, 0px);">

                <div class="swiper-slide testimony__card p-3 swiper-slide-active" style="width: 320px;" data-swiper-slide-index="0">

                  <blockquote class="blockquote shadow">
                    <span class="rating text-warning d-block mb-4">
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                    </span>

                    <p class="mb-4">+rep been here since the beginning, basically all of my suggestions were implemented into KeyAuth</p>

                    <footer class="blockquote-footer d-flex align-items-center">
                      <div class="testimony__avatar d-inline-block mr-3">
                        <img class="rounded-circle" src="https://keyauth.com/static/images/administrator.png" srcset="https://keyauth.com/static/images/administrator.png" alt="Avatar">
                      </div>

                      <div class="testimony__info d-inline-block">

                        <span class="info-name d-block">Administrator</span>
                        <span class="info-company d-block">Seller Subscription</span>
                      </div>

                    </footer>
                  </blockquote>

                </div>

                <div class="swiper-slide testimony__card p-3 swiper-slide-next" style="width: 320px;" data-swiper-slide-index="1">

                  <blockquote class="blockquote shadow">
                    <span class="rating text-warning d-block mb-4">
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                    </span>

                    <p class="mb-4">+rep pretty solid and some good examples</p>

                    <footer class="blockquote-footer d-flex align-items-center">
                      <div class="testimony__avatar d-inline-block mr-3">
                        <img class="rounded-circle" src="https://keyauth.com/static/images/ktown.png" srcset="https://keyauth.com/static/images/ktown.png" alt="Avatar">
                      </div>

                      <div class="testimony__info d-inline-block">

                        <span class="info-name d-block">Ktown</span>
                        <span class="info-company d-block">Seller Subscription</span>
                      </div>

                    </footer>
                  </blockquote>

                </div>

                <div class="swiper-slide testimony__card p-3" style="width: 320px;" data-swiper-slide-index="2">

                  <blockquote class="blockquote shadow">
                    <span class="rating text-warning d-block mb-4">
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                      <i class="fas fa-star"></i>
                    </span>

                    <p class="mb-4">+rep best auth in community definitely would recommend it 100%</p>

                    <footer class="blockquote-footer d-flex align-items-center">
                      <div class="testimony__avatar d-inline-block mr-3">
                        <img class="rounded-circle" src="https://keyauth.com/static/images/kairo.gif" srcset="https://keyauth.com/static/images/kairo.gif" alt="Avatar">
                      </div>

                      <div class="testimony__info d-inline-block">

                        <span class="info-name d-block">KAIRO</span>
                        <span class="info-company d-block">Developer Subscription</span>
                      </div>

                    </footer>
                  </blockquote>

                </div>

               

              </div> 
			  
              <div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets"><span class="swiper-pagination-bullet swiper-pagination-bullet-active" tabindex="0" role="button" aria-label="Go to slide 1"></span><span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide 2"></span><span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide 3"></span><span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide 4"></span></div>

              <div class="swiper-button-prev rounded" tabindex="0" role="button" aria-label="Previous slide"></div>
              <div class="swiper-button-next rounded" tabindex="0" role="button" aria-label="Next slide"></div>

            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
        </section>
        <section class="section section-lg bg-primary text-white">
            <div class="container" id="features">
                <div class="row justify-content-center mb-5 mb-lg-6">
                    <div class="col-12 text-center">
                        <h2 class="h1 px-lg-5">Several Features</h2>
                        <p class="lead px-lg-8">You get all Bootstrap components fully customized. Besides, you receive numerous plugins out of the box and ready to use.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card bg-white shadow-soft text-primary rounded mb-4">
                            <div class="px-3 px-lg-4 py-5 text-center">
                                <span class="icon icon-lg mb-4"><i class="fas fa-lock"></i></span>
                                <h5 class="font-weight-normal text-primary">AES-256 Encryption</h5>
                                <p>All requests sent to and from the server are AES-256 Encrypted and then Hex encoded</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card bg-white shadow-soft text-primary rounded mb-4">
                            <div class="px-3 px-lg-4 py-5 text-center">
                                <span class="icon icon-lg mb-4"><i class="fas fa-user-lock"></i></span>
                                <h5 class="font-weight-normal text-primary">Optional Hardware Lock</h5>
                                <p>Enable the Hardware Lock to require the server to check if HWID matches</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card bg-white shadow-soft text-primary rounded mb-4">
                            <div class="px-3 px-lg-4 py-5 text-center">
                                <span class="icon icon-lg mb-4"><i class="fas fa-user-slash"></i></span>
                                <h5 class="font-weight-normal text-primary">Hardware Blacklist</h5>
                                <p>Blacklist your client's hardware info such as Windows SID and IP address</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card bg-white shadow-soft text-primary rounded mb-4">
                            <div class="px-3 px-lg-4 py-5 text-center">
                                <span class="icon icon-lg mb-4"><i class="fas fa-user-shield"></i></span>
                                <h5 class="font-weight-normal text-primary">DDoS protection</h5>
                                <p>KeyAuth mitigates DDoS attacks swiftly, preventing any disruption to your user's during authentication</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card bg-white shadow-soft text-primary rounded mb-4">
                            <div class="px-3 px-lg-4 py-5 text-center">
                                <span class="icon icon-lg mb-4"><i class="fas fa-link"></i></span>
                                <h5 class="font-weight-normal text-primary">Server-sided Webhooks</h5>
                                <p>Protect your API link(s) by having KeyAuth send requests on the server-side.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card bg-white shadow-soft text-primary rounded mb-4">
                            <div class="px-3 px-lg-4 py-5 text-center">
                                <span class="icon icon-lg mb-4"><i class="fas fa-server"></i></span>
                                <h5 class="font-weight-normal text-primary">Server-sided Variables</h5>
                                <p>Store strings securely on the server-side, only allowing access to them after authentication</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card bg-white shadow-soft text-primary rounded mb-4">
                            <div class="px-3 px-lg-4 py-5 text-center">
                                <span class="icon icon-lg mb-4"><i class="fas fa-cloud-download-alt"></i></span>
                                <h5 class="font-weight-normal text-primary">Server-sided Files</h5>
                                <p>Upload files securely on the server-side, only allowing users to download after authentication</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card bg-white shadow-soft text-primary rounded mb-4">
                            <div class="px-3 px-lg-4 py-5 text-center">
                                <span class="icon icon-lg mb-4"><i class="fas fa-laptop-code"></i></span>
                                <h5 class="font-weight-normal text-primary">SellerAPI</h5>
                                <p>API endpoint exclusive to customers with the Seller role, access to all KeyAuth resources outbound</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section section-lg bg-soft" >
    <div class="container" id="pricing">
        <div class="row">
            <div class="col-12 col-lg-8">
                <h2 class="h1 font-weight-light mb-3"><strong>Open-source</strong> project</h2>
                <p class="lead mb-4">KeyAuth is an open-source project. There are also cloud-hosted subscriptions for people looking to forego the investment of time and money attributed to self-hosting.</p>
                <div class="d-flex align-items-center">
                    <a href="https://github.com/KeyAuth/KeyAuth-Source-Code" target="_blank" class="btn btn-secondary mr-4 animate-up-2">
                        View on GitHub
                    </a>
                    <!-- Place this tag where you want the button to render. -->
                    <div class="mt-2">
                        <!-- Place this tag where you want the button to render. -->
                        <a class="github-button" href="https://github.com/KeyAuth/KeyAuth-Source-Code" data-color-scheme="no-preference: dark; light: light; dark: light;" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star themesberg/pixel-bootstrap-ui-kit on GitHub">Star</a>                            
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="github-big-icon">
                  <span class="fab fa-github"></span>
                </div>
              </div>
        </div>
        <div class="row mt-6">
	<div class="col-12 col-lg-4">
		<!-- Card -->
		<div class="card shadow-soft mb-5 mb-lg-6 px-2">
			<div class="card-header border-light py-5 px-4">
				<!-- Price -->
				<div class="d-flex mb-3"><span class="h5 mb-0">$</span> <span class="price display-2 mb-0" data-annual="0" data-monthly="0">0</span> <span class="h6 font-weight-normal align-self-end">/year</span></div>
				<h4 class="mb-3 text-black">Tester Subscription</h4>
				<p class="font-weight-normal mb-0">Limited Access for those looking to experiment implementing KeyAuth</p>
			</div>
			<div class="card-body pt-5">
				<ul class="list-group simple-list">
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Users<span class="font-weight-bolder"> 50</span></li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-times"></i></span>Upload Files</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-times"></i></span>Create Webhooks</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Variables</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Logs</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Hardware Blacklist</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-times"></i></span>Reseller & Manager Accounts</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-times"></i></span>SellerAPI Access</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-times"></i></span>Discord Bot</li>
				</ul>
			</div>
			<div class="card-footer px-4 pb-4">
				<!-- Button --><a href="https://keyauth.com/register" class="btn btn-block btn-outline-gray animate-up-2">Start for Free <span class="icon icon-xs ml-3"><i class="fas fa-arrow-right"></i></span></a></div>
		</div>
	</div>
	<div class="col-12 col-lg-4">
		<!-- Card -->
		<div class="card shadow-soft mb-5 mb-lg-6">
			<div class="card-header border-light py-5 px-4">
				<!-- Price -->
				<div class="d-flex mb-3 text-primary"><span class="h5 mb-0">$</span> <span class="price display-2 text-primary mb-0" data-annual="199" data-monthly="99">9.99</span> <span class="h6 font-weight-normal align-self-end">/year</span></div>
				<h4 class="mb-3 text-black">Developer Subscription</h4>
				<p class="font-weight-normal mb-0">Ample limits plus full access to reseller system. Most folks start here.</p>
			</div>
			<div class="card-body pt-5">
				<ul class="list-group simple-list">
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Users<span class="font-weight-bolder"> Unlimited</span></li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Upload Files</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Create Webhooks</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Variables</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Logs</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Hardware Blacklist</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Reseller & Manager Accounts</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-times"></i></span>SellerAPI Access</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-times"></i></span>Discord Bot</li>
				</ul>
			</div>
			<div class="card-footer px-4 pb-4">
				<!-- Button --><a href="https://keyauth.com/dashboard/account/upgrade/" class="btn btn-block btn-outline-primary">Go to Developer<span class="icon icon-xs ml-3"><i class="fas fa-arrow-right"></i></span></a></div>
		</div>
	</div>
	<div class="col-12 col-lg-4">
		<!-- Card -->
		<div class="card shadow-soft border-light mb-5 mb-lg-6">
			<div class="card-header border-light py-5 px-4">
				<!-- Price -->
				<div class="d-flex mb-3"><span class="h5 mb-0">$</span> <span class="price display-2 text-secondary mb-0" data-annual="299" data-monthly="199">19.99</span> <span class="h6 font-weight-normal align-self-end">/year</span></div>
				<h4 class="mb-3 text-black">Seller Subscription</h4>
				<p class="font-weight-normal mb-0">Full-fledged supporter, we appreciate you for keeping our servers running!</p>
			</div>
			<div class="card-body pt-5">
				<ul class="list-group simple-list">
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Users<span class="font-weight-bolder"> Unlimited</span></li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Upload Files</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Create Webhooks</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Variables</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Logs</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Hardware Blacklist</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Reseller & Manager Accounts</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>SellerAPI Access</li>
					<li class="list-group-item font-weight-normal"><span class="icon-gray"><i class="fas fa-check"></i></span>Discord Bot</li>
				</ul>
			</div>
			<div class="card-footer px-4 pb-4">
				<!-- Button --><a href="https://keyauth.com/dashboard/account/upgrade/" class="btn btn-block btn-outline-secondary">Start with Seller<span class="icon icon-xs ml-3"><i class="fas fa-arrow-right"></i></span></a></div>
		</div>
	</div>
</div>
    </div>
</section>

        <footer class="footer section pt-6 pt-md-8 pt-lg-10 pb-3 bg-primary text-white overflow-hidden">
    <div class="pattern pattern-soft top"></div>
    <div class="container">
        <div class="row">
            <div class="col-6 col-sm-3 col-lg-2 mb-4 mb-lg-0">
                <ul class="links-vertical">
                    <li><a target="_blank" href="https://docs.keyauth.com/">Documentation</a></li>
                    <li><a target="_blank" href="https://stats.uptimerobot.com/2DrzGFk4PY">Server Status</a></li>
                    <li><a target="_blank" href="https://keyauth.com/discord">Contact Us</a></li>
                </ul>
            </div>
            <div class="col-6 col-sm-3 col-lg-2 mb-4 mb-lg-0">
                <h6></h6>
                <ul class="links-vertical">
                    <li><a target="_blank" href="https://keyauth.com/discord">Support</a></li>
                    <li><a target="_blank" href="https://github.com/KeyAuth/KeyAuth-Source-Code/blob/main/LICENSE.txt">License</a>
                    </li>
                </ul>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
				<iframe src="https://discordapp.com/widget?id=824397012685291520&theme=dark" width="250" height="300" allowtransparency="true" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"></iframe>
            </div>
        </div>
        <hr class="my-4 my-lg-5">
    </div>
</footer>

    </main>

    <!-- Core -->
<script src="https://cdn.keyauth.com/vendor/jquery/dist/jquery.min.js"></script>
<script src="https://cdn.keyauth.com/vendor/popper.js/dist/umd/popper.min.js"></script>
<script src="https://cdn.keyauth.com/vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.keyauth.com/vendor/headroom.js/dist/headroom.min.js"></script>

<!-- swiper for reviews -->
<script src="https://cdn.keyauth.com/vendor/swiper/swiper.min.js"></script>
<script src="https://cdn.keyauth.com/vendor/swiper/wb.swiper-init.js"></script>

<!-- Vendor JS -->
<script src="https://cdn.keyauth.com/vendor/onscreen/dist/on-screen.umd.min.js"></script>
<script src="https://cdn.keyauth.com/vendor/waypoints/lib/jquery.waypoints.min.js"></script>
<script src="https://cdn.keyauth.com/vendor/jarallax/dist/jarallax.min.js"></script>
<script src="https://cdn.keyauth.com/vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js"></script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>

<!-- Impact JS -->
<script src="https://cdn.keyauth.com/front/assets/js/front.js"></script>

    
</body>

</html>
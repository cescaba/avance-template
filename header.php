<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<header class="site-header">
		<div class="container">
			<div class="site-branding">
				<?php if ( has_custom_logo() ) the_custom_logo(); ?>
				<h1 class="site-title">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php echo avance_nativo_get_site_title(); ?>
					</a>
				</h1>
				<?php $desc = avance_nativo_get_site_description(); if ( $desc ) echo '<p class="site-description">' . $desc . '</p>'; ?>
			</div>

			<button class="menu-toggle" id="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
				☰ Menu
			</button>

			<nav class="primary-navigation" id="primary-menu">
				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'fallback_cb' => false ) ); ?>
			</nav>
		</div>
	</header>

	<main id="main" class="site-main">

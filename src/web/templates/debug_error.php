<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

/** @var $request array **/
/** @var $number int **/
/** @var $message string **/
/** @var $file string **/
/** @var $line int **/
/** @var $snippet array **/

use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Support\Enums\PHPErrorLevels;

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<title><?= PHPErrorLevels::tryFrom($number)?->label() ?? 'Unknown Error' ?></title>
	<meta name="theme-color" content="#F6F6F6">
	<meta name="color-scheme" content="light dark">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=1, viewport-fit=cover">
	<link rel="stylesheet" href="/vendor/rovota/framework/src/web/assets/styles/theming.css">
	<link rel="stylesheet" href="/vendor/rovota/framework/src/web/assets/styles/debug.css">
</head>
<body class="theme-automatic accent-default">

<container>

	<nav>
		<ul>
			<li><?= $request['full_url'] ?? 'Unknown URL' ?></li>
			<li><a href="https://rovota.gitbook.io/core" target="_blank" rel="noreferrer">Documentation</a></li>
		</ul>
	</nav>

	<header>

		<card class="details">
			<div class="name">
				<span><?= PHPErrorLevels::tryFrom($number)?->label() ?? 'Unknown Error' ?></span>
			</div>
			<h1><?= $message ?? 'There is no message available' ?></h1>
			<hr>
			<p>
				<span>PHP <?= PHP_VERSION ?></span>
				<span>Core <?= Framework::version()->basic() ?></span>
			</p>
		</card>

	</header>

	<main>

		<card class="stack">

			<preview>
				<heading>
					<span><?= str_replace('\\', '\\<wbr>', $file) ?><small>:<?= $line ?></small></span>
				</heading>
				<file>
					<table>
						<tr class="empty">
							<td></td>
							<td></td>
						</tr>
						<?php
						foreach ($snippet as $number => $content) {
							$number++;
							$class = '';
							if ($number === $line) {
								$class = 'highlight';
							}
							if (str_starts_with(trim($content), '//')) {
								$class = 'comment';
							}
							if ($number < ($line - 10)) {
								continue;
							}
							if ($number > ($line + 10)) {
								break;
							}
							?>
							<tr class="<?= $class ?>">
								<td><?= $number ?></td>
								<td>
									<pre><code><?= htmlentities(str_replace('	', '    ', $content), encoding: 'UTF-8') ?></code></pre>
								</td>
							</tr>
						<?php
						} ?>
						<tr class="empty">
							<td></td>
							<td></td>
						</tr>
					</table>
				</file>
			</preview>

		</card>

	</main>

</container>

</body>
</html>
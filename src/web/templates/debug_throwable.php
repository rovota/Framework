<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

/** @var $throwable Throwable **/
/** @var $fatal bool **/
/** @var $request array **/
/** @var $traces array **/
/** @var $snippet array **/

use Rovota\Framework\Kernel\Framework;
use Rovota\Framework\Support\Interfaces\Solution;

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<title><?= $throwable::class ?></title>
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
				<span class="unhandled">Unhandled</span>
				<span><?= $throwable::class ?></span>
			</div>
			<h1><?= $throwable->getMessage() ?? 'There is no message available' ?></h1>
			<hr>
			<p>
				<span>PHP <?= PHP_VERSION ?></span>
				<span>Core <?= Framework::version()->basic() ?></span>
			</p>
		</card>

		<?php
		if (isset($solution) && $solution instanceof Solution) { ?>
			<card class="solution">
				<p><b><?= str_replace('\\', '\\<wbr>', htmlentities($solution->title())) ?></b></p>
				<p><?= $solution->description() ?></p>
				<?php
				foreach ($solution->references() as $link_title => $link_url) {
					echo sprintf('<p><a href="%s" class="accent-neutral">%s</a></p>', $link_url, $link_title);
				}
				?>
			</card>
		<?php
		} ?>

	</header>

	<main>

		<card class="stack">

			<traces>
				<heading>
					<span>Stack Trace</span>
				</heading>
				<content>
				<?php
				foreach ($traces as $trace) { ?>
					<trace>
						<span><?= str_replace('\\', '\\<wbr>', $trace['class'] ?? $trace['file']) ?><small>:<?= $trace['line'] ?></small></span>
						<span><b><?= $trace['type'].$trace['function'] ?></b></span>
					</trace>
				<?php
				} ?>
				</content>
			</traces>

			<preview>
				<heading>
					<span><?= str_replace('\\', '\\<wbr>', $throwable->getFile()) ?><small>:<?= $throwable->getLine() ?></small></span>
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
							if ($number === $throwable->getLine()) {
								$class = 'highlight';
							}
							if (str_starts_with(trim($content), '//')) {
								$class = 'comment';
							}
							if ($number < ($throwable->getLine() - 10)) {
								continue;
							}
							if ($number > ($throwable->getLine() + 10)) {
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
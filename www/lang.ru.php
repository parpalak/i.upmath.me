<?php
/**
 * Russian interface
 *
 * @copyright 2015-2024 Roman Parpalak
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @package   Upmath Latex Renderer
 * @link      https://i.upmath.me
 */

return [
	'title'                 => 'Картинки для веба из формул на латехе',
	'meta-keywords'         => 'LaTeX, латех, формулы, SVG-картинки, конвертировать формулы на летехе в картинки',
	'meta-description'      => 'Сервис преобразует формулы на латехе в картинки для публикации в интернете.',
	'header'                => 'Картинки для&nbsp;веба из&nbsp;формул на&nbsp;латехе',
	'equation editor'       => 'Редактор формул',
	'formula in latex'      => 'Формула на латехе',
	'image URL'             => 'Адрес&nbsp;картинки:&nbsp;',
	'examples'              => 'Примеры',
	'examples info'         => 'Слева &mdash; образцы кода на латехе, справа &mdash; результат.',
	'add to editor'         => 'Добавить в редактор',
	'link-s2'               => '&larr; Движок S2',
	'link-faq'              => 'Вопросы и ответы',
	'link-install'          => 'Установка на сайты',
	'page-editor'           => 'Upmath: редактор текстов',
	'samples'               => [
		'integrals'  => 'Интегралы, корни и рамки',
		'limits'     => 'Пределы и суммы',
		'chains'     => 'Цепные дроби',
		'matrices'   => 'Матрицы',
		'align'      => 'Многострочные формулы',
		'picture'    => 'Картинки в окружении <code>picture</code>',
		'xy-pics'    => 'Диаграммы <code>xy-pic</code>',
		'tikz'       => 'Чертежи TikZ',
		'tikz-plots' => 'Графики TikZ',
	],
	'faq section'           => '
			<h2>Вопросы и ответы</h2>

			<div class="info-text">
			<div class="question">
				<h3>Что такое &laquo;латех&raquo;?</h3>
				<p>Латех &mdash; это система компьютерной верстки сложных документов. Широко используется в&nbsp;науке,
				стандарт <nobr>де-факто</nobr> в&nbsp;математических и&nbsp;физических журналах.
				<a href="http://ru.wikipedia.org/wiki/LaTeX">Подробности&nbsp;&mdash; в&nbsp;википедии</a>.</p>
			</div>

			<div class="question">
				<h3>Что делает этот сайт?</h3>
				<p>
					Сайт превращает математические формулы на&nbsp;латехе в&nbsp;готовые для веба картинки.
					Их не&nbsp;нужно создавать в&nbsp;графическом редакторе и&nbsp;загружать куда&nbsp;бы то ни было.
					Добавляйте картинки к&nbsp;обсуждениям в&nbsp;блогах и&nbsp;форумах или&nbsp;пересылайте ссылки в&nbsp;личной переписке.
				</p>
			</div>

			<div class="question">
				<h3>Сколько это стоит?</h3>
				<p>При разумном использовании нисколько. Разумность использования определяется просто:
				если вы мешаете другим пользователям, ваш поток запросов будет заблокирован.</p>
			</div>

			<div class="question">
				<h3>А сервис надежный?</h3>
				<p>Вполне. Вы можете посмотреть данные о&nbsp;доступности, собранные <a href="https://stats.uptimerobot.com/YVrX5ik0A5">независимым внешним сервисом UptimeRobot</a>.</p>
			</div>

			<div class="question">
				<h3>Есть гарантия, что сервис не&nbsp;перестанет работать?</h3>
				<p>Нет. Но я сам использую его на&nbsp;своих сайтах и&nbsp;не&nbsp;собираюсь закрывать.</p>
			</div>

			<div class="question">
				<h3>Как формулы превращаются в&nbsp;картинки?</h3>
				<p>
					На&nbsp;сервере установлен <a href="https://ru.wikipedia.org/wiki/TeX_Live">Tex Live</a>.
					Он работает <a href="https://parpalak.com/articles/technologies/site_building/latex_for_web">
					в&nbsp;связке с&nbsp;современными веб-технологиями</a>.
				</p>
			</div>

			<div class="question">
				<h3>Кириллица и иероглифы в формулах не отображаются!</h3>
				<p>Возможно, вам поможет команда \text. Например, <code>Q_\text{плавления}>0</code> отображается правильно: $$Q_\text{плавления}>0$$.</p>
			</div>

			<div class="question">
				<h3>Как подключать пакеты латеха? Я хочу картинки с химическими формулами и нотами!</h3>
				<p>При создании картинок подключается минимальный набор пакетов. Если какого-то пакета вам не хватает, напишите мне письмо. Не забудьте объяснить, как пакет пригодится другим пользователям.</p>
			</div>

			<div class="question">
				<h3>Все формулы нужно набирать в этом редакторе?</h3>
				<p>Несколько формул удобнее набирать на этой странице. Текст с формулами набирайте
				в&nbsp;<a href="https://upmath.me/">редакторе с&nbsp;поддержкой маркдаун-разметки</a>
				и&nbsp;получайте готовый html-код. Сайты с&nbsp;математическими текстами могут использовать сервис напрямую.</p>
			</div>
			</div>
	',
	'embedding section 1'   => '
			<h2>Встраивание математических формул на&nbsp;сайты</h2>

			<div class="info-text">
			<p>
				Авторы математических текстов могут включать формулы на&nbsp;латехе сразу в&nbsp;код страниц.
				Чтобы при&nbsp;загрузке сайта формулы заменялись картинками, их нужно писать в&nbsp;двойных долларах: <code><span>$$</span>...$$</code>, и&nbsp;в&nbsp;исходном коде страниц подключать скрипт
			</p>
	',
	'embedding section 2'   => '
			<p>Пример html-кода:</p>
	',
	'embedding section 2.1' => '
			<p>Получившийся результат:</p>
	',
	'embedding section 3'   => '
			<div class="question">
				<p>В современных браузерах скрипт загружает векторные картинки в&nbsp;формате SVG
				и&nbsp;выравнивает базовые линии формул и&nbsp;окружающего текста:</p>

				<p align="center"><img src="/i/baseline.png" alt="" width="400" height="230" class="screenshot" style="max-width: 90vw; max-height: 51.75vw;" /></p>
			</div>
			<p>
				На этом сервисе работает мой <a href="https://susy.page/">блог о&nbsp;теоретической физике</a>.
			</p>
			</div>
	',
	'copyright section'     => <<<TEXT
				&copy; <a href="https://parpalak.com/">Роман Парпалак</a>, 2014&ndash;2024.
				<script>var mailto="roman%"+"40parpalak.com";document.write('Пишите: <a href="mailto:'+unescape(mailto)+'">' + unescape(mailto) + '</a>.');</script>
TEXT
	,
];

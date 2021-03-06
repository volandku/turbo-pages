<?php
/**
 * Created by PhpStorm.
 * User: exstreme
 * Date: 07.12.2017
 * Time: 20:06
 * Link: https://protectyoursite.ru
 * Version: 1.0.0
 */
define( '_JEXEC', 1 );
if ( file_exists( __DIR__ . '/defines.php' ) ) {
    include_once __DIR__ . '/defines.php';
}
if ( !defined( '_JDEFINES' ) ) {
    define( 'JPATH_BASE', __DIR__ );
    require_once JPATH_BASE . '/includes/defines.php';
}
require_once JPATH_BASE . '/includes/framework.php';
$app = JFactory::getApplication('site');

$siteurl="https://protectyoursite.ru"; // Задаём адрес сайта
/* Выборка с базы данных, добавить необходимые параметры или закомментировать лишние */
$db = JFactory::getDbo();
$query = $db->getQuery( true )
    ->select($db->quoteName('id'))
    ->select($db->quoteName('catid'))
    ->select($db->quoteName('title'))
    ->select($db->quoteName('publish_up'))
    ->select($db->quoteName('introtext'))
    ->select($db->quoteName('fulltext'))
    ->from( '#__content' )
    ->where('state=1') // Только опубликованные материалы
    ->where('access=1') // Доступные для всех
    ->where("catid IN ('2','9')"); // Задаём отдельные категории
$list = $db->setQuery( $query )->loadObjectList();

$xml='<?xml version="1.0" encoding="utf-8"?>
<rss
    xmlns:yandex="http://news.yandex.ru"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:turbo="http://turbo.yandex.ru"
    version="2.0">
	<channel>
		<title>Удаление вирусов на сайте</title>
		<description><![CDATA[Чистка вебсайтов от вирусов на Joomla, Wordpress, Modx, Drupal, Magento, Bitrix и других CMS, установка защиты, устранение уязвимостей, гарантия на работу.]]></description>
		<link>'.$siteurl.'/</link>
		<lastBuildDate>'.date(DATE_ATOM).'</lastBuildDate>
		<language>ru-ru</language>
		<managingEditor>info@protectyoursite.ru (Защита сайтов от вирусов и уязвимостей)</managingEditor>';
foreach($list as $item) {
    $xml.='
			<item turbo="true">
			<title>'.$item->title.'</title>
			<link>'.$siteurl.\Joomla\CMS\Router\Route::_('index.php?option=com_content&view=article&id='.$item->id.'&catid='.$item->catid).'</link>
			<turbo:content><![CDATA['.str_ireplace('src="images','src="'.$siteurl.'/images',$item->introtext);
    $xml.=$item->fulltext ? str_ireplace('src="images','src="'.$siteurl.'/images',$item->fulltext) : '';
    $xml.=']]></turbo:content>
			<author>Protect Your Site</author>
			<pubDate>'.$item->publish_up.'</pubDate>
		</item>';
}
$xml.='</channel>
</rss>';
//echo $xml; 
if (file_put_contents($_SERVER['DOCUMENT_ROOT'].'/turbo.xml', $xml))
{
    echo "XML файл сгенерирован";
}
else
{
    echo "Неизвестная ошибка";
}
?>
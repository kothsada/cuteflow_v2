<?php
/*
 * Copyright by Uwe Klawitter, Timo Haberkern, Stefan Brommer
 * 
 * License:   GNU General Public License 2 (GPL 2) or later
 * 
 * This program is free software; you can redistribute it
 * and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later
 * version.
 * 
 * This program is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied
 * warrenty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 * PURPOSE.
 * See the GNU General Public License for more details.
 *
 */
class RssFeed
{
	private $arrItems = array();
	
	private $strDesc = '';
	private $strLink = '';
	private $strTitle = '';
	
	/**
	 * Instantiates a new rss-feed.
	 */
	public function __construct() {}
	
	/**
	 * Generates a string to display the rss-feed.
	 *
	 * @return string
	 */
	public function __toString()
	{
		$objDom = new DOMDocument('1.0', 'utf-8');
		
		// create root element
		$objRoot = $objDom->createElement('rss');
		$objRoot->setAttributeNode(new DOMAttr('version', '2.0'));
		$objDom->appendChild($objRoot);
		
		// append channel to root element
		$objChannel = $objDom->createElement('channel');
		$objRoot->appendChild($objChannel);
		
		// set rss-title
		$objTitle = $objDom->createElement('title');
		$objTitle->appendChild($objDom->createTextNode($this->strTitle));
		$objChannel->appendChild($objTitle);
		
		// set rss-description
		$objDesc = $objDom->createElement('description');
		$objDesc->appendChild($objDom->createTextNode($this->strDesc));
		$objChannel->appendChild($objDesc);
		
		
		// set rss-link
		$objLink = $objDom->createElement('link');
		$objLink->appendChild($objDom->createTextNode($this->strLink));
		$objChannel->appendChild($objLink);
		
		foreach ($this->arrItems as $objEpRssItem)
		{
			// append item
			$objItem = $objDom->createElement('item');
			$objChannel->appendChild($objItem);
			
			// append title to item
			$objItemTitleCDATA = $objDom->
				createTextNode($objEpRssItem->getTitle());
			$objItemTitle = $objDom->createElement('title');
			$objItemTitle->appendChild($objItemTitleCDATA);
			$objItem->appendChild($objItemTitle);
			
			// append description to item
			$objItemDescCDATA = $objDom->
				createTextNode($objEpRssItem->getDescription());
			$objItemDesc = $objDom->createElement('description');
			$objItemDesc->appendChild($objItemDescCDATA);
			$objItem->appendChild($objItemDesc);

			// append link to item
			$objItemLinkCDATA = $objDom->
				createTextNode($objEpRssItem->getLink());
			$objItemLink = $objDom->createElement('link');
			$objItemLink->appendChild($objItemLinkCDATA);
			$objItem->appendChild($objItemLink);
		}
		
		return utf8_encode($objDom->saveXML());
	}
	
	/**
	 * Add a new item to the rss-feed
	 *
	 * @param RssFeedItem $objItem
	 */
	public function addItem(RssFeedItem $objItem)
	{
		$this->arrItems[] = $objItem;
	}
	
	/**
	 * Set the title of this rss-feed
	 *
	 * @param unknown_type $strTitle
	 */
	public function setTitle($strTitle)
	{
		if (is_string($strTitle) == false)
		{
			throw new Exception('The title has to '.
				'be a string');
		}
		
		$this->strTitle = $strTitle;
	}
	
	/**
	 * Set the description of the rss-feed
	 *
	 * @param string $strDesc
	 */
	public function setDescription($strDesc)
	{
		if (is_string($strDesc) == false)
		{
			throw new Exception('The description has to '.
				'be a string');
		}
		
		$this->strDesc = $strDesc;
	}
	
	public function setLink($strLink) {
		if (is_string($strLink) == false)
		{
			throw new Exception('The link has to '.
				'be a string');
		}
		
		$this->strLink = $strLink;
	}
}

?>
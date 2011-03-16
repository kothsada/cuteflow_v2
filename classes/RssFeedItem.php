<?php
/*
 * Copyright by Uwe KLawitter, Timo Haberkern, Stefan Brommer
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
 */
class RssFeedItem
{
	private $strDesc = '';
	private $strLink = '';
	private $strTitle = '';
	
	/**
	 * Instantiates a new RssFeet-Item
	 */
	public function __construct(){}
	
	/**
	 * returns the title of the rss-item
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->strTitle;
	}
	
	/**
	 * returns the description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->strDesc;
	}
	
	public function getLink()
	{
		return $this->strLink;
	}
	
	/**
	 * set the title
	 *
	 * @param string $strTitle
	 */
	public function setTitle($strTitle)
	{
		if (is_string($strTitle) == false)
		{
			throw new Exception();
		}
		
		$this->strTitle = $strTitle;
	}
	
	/**
	 * set the description
	 *
	 * @param string $strDesc
	 */
	public function setDescription($strDesc)
	{
		if (is_string($strDesc) == false)
		{
			throw new Exception();
		}
		
		$this->strDesc = $strDesc;
	}
	
	public function setLink($strLink)
	{
		if (is_string($strLink) == false)
		{
			throw new Exception();
		}
		
		$this->strLink = $strLink;
	}
}

?>
<?php

namespace Utility;

/*
Author: SM
Purpose: Utility functions
*/

/**
* function to key in an array
* @param string $sId
* @param array $aArr
* @return string or false if key not defined
*/
function check($sId, $aArr)
{
	return isset($aArr[$sId]) ? $aArr[$sId] : false;
}

/**
* function to key in an array
* @param string $sId
* @param array $aArr
* @return string or false if key not defined
*/
function updated($sId, $aArr1, $aArr2)
{
	return empty($aArr1[$sId]) ? false : $aArr1[$sId] !== $aArr2[$sId];
}
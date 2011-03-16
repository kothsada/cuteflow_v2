<?php
    function getDelayColor($nDays)
    {
		global $DELAY_NORMAL, $DELAY_INDERMIDIATE;

        if ($nDays <= $DELAY_NORMAL)
        {
            return "#019A10";
        }
        else if ($nDays <= $DELAY_INDERMIDIATE)
        {
            return "#FF6C00";
        }
        else
        {
            return "#F70415";
        }
    }
    
    // TODO: Useless, you can use basename function instead
    function getFileNameFromPath($strPath)
    {
        $strFileName = basename($strPath);
        return $strFileName;
    }
?>
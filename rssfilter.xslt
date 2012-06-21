<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="xml" encoding="utf-8" indent="yes"/>

<!-- First, get everything. -->
<xsl:template match="node() | @*">
  <xsl:copy>
    <xsl:apply-templates select="node() | @*"/>
  </xsl:copy>
</xsl:template>

<!-- Then restrict to just certain items. -->
<xsl:template match="/rss/channel/item">
  <xsl:if test="title[contains(., '[Hello]')]">
    <xsl:if test="title[contains(., 'World') or
	   	    ( contains(., 'this') and
	   	      contains(., 'test') and
		      contains(., 'yeah') 
		    ) or
		    ( contains(., 'Another') and
		      contains(., 'HiHi')
		    ) 		    
		    
		    
		  ]">
      <item>
        <xsl:apply-templates select="node()" />
      </item>
    </xsl:if>
  </xsl:if>
  <xsl:if test="title[contains(., 'HelloAgain')]">
    <xsl:if test="title[contains(., 'DUMMYHEHEHE') or
	   	    ( contains(., 'Dienstag') and
	   	      contains(., 'Abend')
		    ) or
		      contains(., 'Bilderbuch')
		     		    
		  ]">
      <item>
        <xsl:apply-templates select="node()" />
      </item>
    </xsl:if>
  </xsl:if>
</xsl:template>
</xsl:stylesheet>

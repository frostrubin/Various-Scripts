<?xml version="1.0" encoding="UTF-8"?> 
<xsl:stylesheet 
  version="1.0" 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns="http://www.w3.org/1999/xhtml"> 
  
  <xsl:output method="xml" indent="yes" encoding="UTF-8"/>
  
  <xsl:template match="/geeklets"> 
    <html> 
      <head>
      <title>Testing XML Example</title>
      <style type="text/css">
        * {padding: none;}
        body {background-image: url(./bg.png);}
      </style>  
      </head> 
      <body> 
        <xsl:apply-templates select="geeklet">
        </xsl:apply-templates> 
      </body> 
    </html> 
  </xsl:template> 
  
  <xsl:template match="geeklet"> 
    <div>
        <div style="position: absolute;
                        left: {xpos};
                         top: {ypos};
                       width: {width};
                      height: {height};
                       color: {txtcolor};
                  background: {background};
                   {freecss}">
      <xsl:value-of select="output"/> 
        </div> 
    </div> 
  </xsl:template>
  
</xsl:stylesheet>
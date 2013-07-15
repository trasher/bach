<?xml version="1.0" encoding="UTF-8"?>
<!--

Displays an EAD fragment as HTML

@author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
@license  Unknown http://unknown.com
@link     http://anaphore.eu
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output method="html" omit-xml-declaration="no"/>

    <xsl:param name="full" select="'false'"/>

    <xsl:template match="c">
        <article>
            <xsl:if test="@id">
                <xsl:attribute name="id">
                    <xsl:value-of select="@id"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:choose>
                <xsl:when test="$full = 'true'">
                    <xsl:apply-templates mode="full"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:apply-templates mode="resume"/>
                </xsl:otherwise>
            </xsl:choose>
        </article>
    </xsl:template>

    <xsl:template match="did" mode="resume">
        <!-- Title is already displayed, show other items -->
        <header class="did">
            <span class="date">
                <xsl:if test="unitdate/@label">
                    <xsl:value-of select="unitdate/@label"/>
                    <xsl:text> : </xsl:text>
                </xsl:if>
                <xsl:value-of select="unitdate"/>
            </span>
        </header>
    </xsl:template>

    <xsl:template match="physdesc" mode="resume">
        <section class="physdesc">
            <xsl:if test="@label">
                <header><xsl:value-of select="@label"/></header>
            </xsl:if>
            <xsl:apply-templates mode="resume"/>
        </section>
    </xsl:template>

    <xsl:template match="genreform|extent" mode="resume">
        <xsl:if test="@label">
            <strong><xsl:value-of select="@label"/></strong>
        </xsl:if>
        <xsl:value-of select="."/>
    </xsl:template>

    <xsl:template match="lb" mode="resume">
        <br/>
    </xsl:template>

    <xsl:template match="controlaccess" mode="resume">
        <aside class="controlaccess">
            <xsl:if test="head">
                <header><xsl:value-of select="head"/></header>
            </xsl:if>
            <div>
                <xsl:apply-templates mode="resume" />
            </div>
        </aside>
    </xsl:template>

    <xsl:template match="subject|geogname|persname|corpname|name" mode="resume">
        <a href="#"><xsl:value-of select="."/></a>
        <xsl:if test="following-sibling::subject or following-sibling::geogname or following-sibling::persname or following-sibling::corpname or following-sibling::name">
            <xsl:text>, </xsl:text>
        </xsl:if>
    </xsl:template>

    <!-- Per default, display nothing -->
    <xsl:template match="*|@*|node()"/>
    <xsl:template match="*|@*|node()" mode="full"/>
    <xsl:template match="*|@*|node()" mode="resume"/>

</xsl:stylesheet>

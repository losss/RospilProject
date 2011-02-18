<?

// channel

// items


?>

<rss version="2.0">
    <channel>
        <title>'. $row['title'] .'</title>
        <link>'. $row['link'] .'</link>
        <description>'. $row['description'] .'</description>
        <language>'. $row['language'] .'</language>
        <image>
            <title>'. $row['image_title'] .'</title>
            <url>'. $row['image_url'] .'</url>
            <link>'. $row['image_link'] .'</link>
            <width>'. $row['image_width'] .'</width>
            <height>'. $row['image_height'] .'</height>
        </image>';
        <item>
            <title>'. $row["title"] .'</title>
            <link>'. $row["link"] .'</link>
            <description><![CDATA['. $row["description"] .']]></description>
        </item>
    </channel>
</rss>
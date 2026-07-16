#!/usr/bin

# remove the old sitemap dumps
echo "removing old sitemap dumps..."
rm -f ../resources/dumps/*

# generate new sitemap dumps
echo "generating new sitemap dumps..."
php ../../../../index.php sitemap_dump_controller dump

# remove the old sitemaps
rm -f ../resources/sitemaps/*

# build the actual sitemaps
echo "building sitemaps..."
cd ../java
ant generate

# the sitemaps are now generated
echo "...done"
#!/usr/bin/env perl

use strict;
use utf8;

use DateTime;
use DateTime::Event::Sunrise;


# use the following from command level to generate 
# the position.data file if needed
#
# curl "https://maps.googleapis.com/maps/api/geocode/json?address=40891 Meadow Vista Place, Lovettsville VA 20180" > position.data

my $filename = 'position.data';

# didn't take the time to parse the json returned (yet!)
my $lat = `grep "lat" position.data | head -1 | sed 's/  */ /g' | cut -d' ' -f4 | sed 's/,//g'`;
my $lon = `grep "lng" position.data | head -1 | sed 's/  */ /g' | cut -d' ' -f4 | sed 's/,//g'`;

chomp($lat);
chomp($lon);


# Current date
my $now = DateTime->now;
$now->set_time_zone("local");

my $fmt = "%H:%M";

# Get sunrise and sunset data
my ($rhr, $rmn, $shr, $smn);
my $sun = DateTime::Event::Sunrise->new (
    longitude => $lon,
    latitude  => $lat,
    precise   => 1
);

my $sunrise = $sun->sunrise_datetime($now)->strftime($fmt);
($rhr, $rmn) = split(/:/, $sunrise, 2);
my $sunset = $sun->sunset_datetime($now)->strftime($fmt);
($shr, $smn) = split(/:/, $sunset, 2);


open (my $fh, '>', 'sunrise_sunset.data');
print $fh $rhr.",".$rmn.",".$shr.",".$smn;
close $fh;


print "Done.\n";


-- This table stores the image data as a blob along with a name for the image.
-- It's a pretty simplistic way of storing image data, but it works well enough.
create table if not exists images
(
    name text not null,
    data blob not null
);


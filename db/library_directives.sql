LOAD DATA INFILE 'library_branch.dat'
INTO TABLE library_branch
FIELDS TERMINATED BY '\t'
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

LOAD DATA INFILE 'borrowers.dat'
INTO TABLE borrower
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

LOAD DATA INFILE 'book.dat'
INTO TABLE book
FIELDS TERMINATED BY '|'
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

LOAD DATA INFILE 'book_authors.dat'
INTO TABLE book_authors
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

LOAD DATA INFILE 'book_copies.dat'
INTO TABLE book_copies
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

LOAD DATA INFILE 'book_loans.dat'
INTO TABLE book_loans
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

update book_authors set type=1;
update book_authors set type=2 where author_name LIKE '%Los Angeles%';
update book_authors set type=2 where author_name LIKE '%Various%';
update book_authors set type=2 where author_name LIKE '%The Beatles%';

alter table book_authors add role varchar(50);
update book_authors set role='Author';
update book_authors set role='Editor' where author_name LIKE '%Editor%';
update book_authors set role='Illustrator' where author_name LIKE '%Illustrator%';
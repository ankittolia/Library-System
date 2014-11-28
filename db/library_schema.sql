drop database if exists library;
create database library;
use library;


drop table if exists book;
create table book(
book_id varchar(10),
title varchar(200) not null,
CONSTRAINT pk_book primary key (book_id)
);

drop table if exists book_authors;
create table book_authors(
book_id varchar(10),
author_name varchar(100) not null,
type int(1) not null default 1,
CONSTRAINT pk_book_authors primary key (book_id,author_name),
CONSTRAINT fk_book_authors foreign key (book_id) references book(book_id)
);


drop table if exists library_branch;
create table library_branch(
branch_id int(2),
branch_name varchar(25) not null,
address varchar(200),
CONSTRAINT pk_branch primary key (branch_id)
);


drop table if exists book_copies;
create table book_copies(
book_id varchar(10),
branch_id int(2),
no_of_copies int(3) not null,
CONSTRAINT pk_book_copies  primary key (book_id,branch_id),
CONSTRAINT fk_book_copies foreign key (book_id) references book(book_id),
CONSTRAINT fk_book_copies_branch foreign key (branch_id) references library_branch(branch_id)
);


drop table if exists borrower;
create table borrower(
card_no int(7),
fname varchar(25) not null,
lname varchar(25) not null,
address varchar(200) not null,
phone varchar(21),
CONSTRAINT pk_borrower primary key (card_no)
);


drop table if exists book_loans;
create table book_loans(
loan_id int(10) AUTO_INCREMENT,
book_id varchar(10),
branch_id int(2),
card_no int(7),
date_out date not null,
due_date date not null,
date_in date,
CONSTRAINT pk_book_loans primary key (loan_id),
CONSTRAINT fk_book_loans_book foreign key (book_id) references book(book_id),
CONSTRAINT fk_book_loans_branch foreign key (branch_id) references library_branch(branch_id),
CONSTRAINT fk_book_loans_borrower foreign key (card_no) references borrower(card_no)
);

drop table if exists fines;
create table fines(
    loan_id int(10),
    fine_amt decimal(6,2),
    paid int(1),
    CONSTRAINT pk_fines primary key (loan_id),
    CONSTRAINT fk_fines_loans foreign key (loan_id) references book_loans(loan_id)
);
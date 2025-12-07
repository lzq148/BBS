create database mybbs;
create table users(

      id int(6) unsigned auto_increment primary key ,
      username varchar(30) not null unique ,
      password varchar(30) not null ,
      name varchar(255),
      level bool not null ,
      img varchar(2555)
);
create table article(
    id int(6) unsigned auto_increment primary key ,
    user_id int unsigned not null ,
    title varchar(255) not null ,
    body text not null ,
    level int(1) not null,
    time datetime not null,
    foreign key (user_id) references users(id)
);



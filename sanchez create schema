#sanchez create schema
#notes------------item table needs to be looked at (not sure what upc or bid/sale data type should be)
#-----------------also constraints need to be implemented
#-----------------catagory has been left out of transactions table idk what it is for
#-----------------Item_desc table should this table reference items or should items reference this?
#-----------------rating table -> ratee should be referencing an account not a user?
#-----------------Message table -> sender and reciver should be referencing an account not a user?
#-----------------hasaddress -> constraints need to be looked at
#-----------------Transactions -> no constraints have been added

drop database if exists sanchez;
create database sanchez;
use sanchez;

#------------has email included
create table Accounts (
	AID varchar(20),
    name varchar(20),
    Password varchar(20),
    email varchar(20),
    primary key(AID)
	);
#------------if unique email per account take out email from top table    
#------------on delete no action ?
create table Emails(
	Email varchar(20),
    AID varchar(20),
    primary key(email),
	foreign key(AID) references accounts(AID) on delete no action);

#---------date accepts yyyy-mm-dd
create table Users(
	UserID varchar(20),
    Gender char(1),
    DateOfBirth date,
    primary key(UserID),
    foreign key(UserID) references accounts(AID) on delete no action );
    
#----------PointOfContact should this be a number?    
create table Suppliers(
	SupplierID varchar(20),
    Revenue real,
    category varchar(20),
    POC varchar(20),
    primary key(SupplierID),
    foreign key(SupplierID) references Accounts(AID) on delete no action);
    
    #not sure about constraints
create table HasPhoneNumber(
	AID varchar(20),
    Pnumber integer(11),
    primary key(AID),
    foreign key(AID) references Accounts(AID) on delete cascade );
    
create table Addresses(
	AddressID int auto_increment,
    Street varchar(10),
    City varchar(10),
    State Char(2),
    zip int(5),
    primary key(AddressID));
# not sure if constraint is correct 
create table HasAddress(
	AID varchar(20) not null,
	AddressID int,
	primary key(AID,AddressID),
	foreign key(AID) references accounts(AID) on delete cascade,
	foreign key(AddressID) references Addresses(AddressID));
    
#---------might want to break experation up into exp_month and exp_year
create table CreditCards(
	UserID varchar(20),
    ccNumber int(16),
    NameOnCard varchar(20),
    ExperationM date,
    3DigitCode int(3),
    Billsto int,
    primary key(UserID,ccNumber),
    foreign key(UserID) references users(UserID),
    foreign key(Billsto) references Addresses(AddressID)
    );
#catagory has been left out idk what it is for
create table Transactions (
	TID int auto_increment,
    TrackingNumber varchar(35),
    DateOfSale date,
    Seller varchar(20),
    Buyer varchar(20),
    PaidWith int(16),
    ShipsTo int,
    shipsFrom int,
    primary key(TID),
    foreign key(Seller) references Accounts(AID),
    foreign key(Buyer) references Users(UserID),
    foreign key(Buyer, PaidWith) references CreditCards(UserID,ccNumber),
    foreign key(shipsTo) references addresses(AddressID),
    foreign key(shipsFrom) references addresses(AddressID)
);
#bid/sale and upc need to be changed
create table Items(
	PID int auto_increment,
	Location varchar(20),
	bid boolean,
	upc int ,
	ListPrice real,
	AuctionPrice real,
	ReservePrice real,
	bidStart timestamp,
	bidEnd timestamp,
	Includedin int,
	primary key (PID),
    foreign key(Includedin) references Transactions(TID)
);    

#should upc reference items
create table Item_Desc(
	UPC int auto_increment,
    Description varchar(300),
    primary key(UPC)
);

create table HasTag(
	Pid int not null,
    tag varchar(10),
    primary key(pid,tag),
    foreign key(pid) references Items(PID)
);

create table categories(
	catagory varchar(20),
    description varchar(100),
    primary key(catagory)
);

create table HasSub(
	Parent varchar(20) not null,
    child varchar(20),
    primary key(Parent, child),
    foreign key(Parent) references categories(catagory),
    foreign key(child) references categories(catagory)
);
#shouldn't ratee be refrencing an account?
create table rating(
	Rater varchar(20),
    ratee varchar(20),
    Stars int(1),
    description varchar(300),
    date date,
    primary key(Rater,Ratee),
    foreign key(Rater) references users(UserID),
    foreign key(ratee) references Users(UserID)
);
#should sender and recever be refencing account?
create table message(
	Sender varchar(20),
    Receiver varchar(20),
    Date date,
    Message varchar(300),
    primary key(Sender, Receiver),
    foreign key(Sender) references Users(UserID),
    foreign key(receiver) references users(UserID)
);

create table HasInCart(
	UserID varchar(20),
    Pid int,
    primary key(UserID,PID),
    foreign key(UserID) references Users(UserID),
    foreign key(Pid) references Items(PID)
);

create table Wishes(
	UserID varchar(20),
    PID int,
    Date date,
    primary key(UserID,PID),
    foreign key(UserID) references Users(UserID),
    foreign key(PID) references Items(PID)
    );

create table Bid(
	UserID varchar(20),
    PID int,
    Date date,
    Amount real,
    primary key(UserID, PID, Date),
    foreign key(UserID) references Users(UserID),
    foreign key(PID) references Items(PID)
);

create table IsIn(
	PID int not null,
    catagory varchar(20),
    primary key(PID, catagory),
    foreign key(catagory) references categories(catagory)
);

create table rateItem(
	Rater varchar(20),
    Pid int,
    Stars int(1),
    Description varchar(300),
    Date date,
    primary key(Rater,PID),
    foreign key(Rater) references Users(UserID),
    foreign key(PID) references Items(PID)
);


create table Owns(
	PID int,
    Owner varchar(20) not null,
    primary key(PID, Owner),
    foreign key(PID) references Items(PID),
    foreign key(Owner) references accounts(AID)
);

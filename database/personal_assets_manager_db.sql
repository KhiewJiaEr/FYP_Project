SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE `personal_assets_manager_db` CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `personal_assets_manager_db`;


CREATE TABLE `currency_tbl` (
  `CurrencyID` int(11) NOT NULL,
  `CurrencyType` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `currency_tbl` (`CurrencyID`, `CurrencyType`) VALUES
(1, '$'),
(2, '€'),
(3, '£'),
(4, '¥'),
(5, 'RM');


CREATE TABLE `user_tbl` (
  `UserID` int(11) NOT NULL,
  `CurrencyID` int(11) NOT NULL,
  `UserRole` enum('Admin','User') DEFAULT NULL,
  `Username` varchar(50) NOT NULL,
  `UserEmail` varchar(50) DEFAULT NULL,
  `UserPassword` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_tbl` (`UserID`, `CurrencyID`, `UserRole`, `Username`, `UserEmail`, `UserPassword`) VALUES
(1, 1, 'Admin', 'Caleb', 'caleb@gmail.com', '202cb962ac59075b964b07152d234b70'),
(2, 1, 'Admin', 'David', 'david@gmail.com', '202cb962ac59075b964b07152d234b70'),
(3, 1, 'User', 'Tester', 'tester@gmail.com', '202cb962ac59075b964b07152d234b70'),
(4, 1, 'User', 'Lisa', 'lisa@gmail.com', '202cb962ac59075b964b07152d234b70'),
(5, 1, 'User', 'Max', 'max@gmail.com', '202cb962ac59075b964b07152d234b70');


CREATE TABLE `category_tbl` (
  `CategoryID` int(11) NOT NULL,
  `CategoryType` varchar(50) NOT NULL,
  `CategoryGroup` enum('Income','Expense') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `category_tbl` (`CategoryID`, `CategoryType`, `CategoryGroup`) VALUES
(1, 'Allowance', 'Income'),
(2, 'Salary', 'Income'),
(3, 'Other Income', 'Income'),
(4, 'Food', 'Expense'),
(5, 'Daily Necessities', 'Expense'),
(6, 'Health', 'Expense'),
(7, 'Other Expense', 'Expense');


CREATE TABLE `transaction_tbl` (
  `TransactionID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `CategoryID` int(11) NOT NULL,
  `Amount` DECIMAL(20, 2) NOT NULL,
  `Note` varchar(500) DEFAULT NULL,
  `DateTime` DATETIME DEFAULT '2023-08-29 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `transaction_tbl` (`TransactionID`, `UserID`, `CategoryID`, `Amount`, `Note`, `DateTime`) VALUES
(1, 4, 1, 300, 'Allowance 1', '2023-09-30 14:30:00'),
(2, 3, 2, 2500, 'Finnaly got my salary', '2023-09-30 14:30:00'),
(4, 3, 3, 1000, '', '2023-09-30 14:30:00'),
(5, 3, 4, 30, 'KFC', '2023-09-30 14:30:00'),
(6, 3, 5, 250, 'Daily Necessities', '2023-09-30 14:30:00'),
(7, 3, 6, 2200, '', '2023-09-30 14:30:00'),
(8, 3, 7, 500, 'Other Expense 1', '2023-09-30 14:30:00'),
(9, 3, 1, 300, 'Allowance 2', '2023-09-30 14:30:00'),
(10, 3, 7, 300, 'Other Expense 2', '2023-09-30 14:30:00'),
(11, 3, 5, 135, 'Next week necessities', '2023-09-30 14:30:00');


CREATE TABLE `investing_tbl` (
  `InvestingID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `InvestingProcess` varchar(100) NOT NULL,
  `InvestingResult` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `investing_tbl` (`InvestingID`, `UserID`, `InvestingProcess`, `InvestingResult`) VALUES
(1, 3, '682559.05*10%+6*10+2', '682559.05*10%+6*10+2 = 68317.91'),
(2, 3, '682559.05*10%+6*(10+2)', '682559.05*10%+6*(10+2) = 68327.91');


ALTER TABLE `currency_tbl`
  ADD PRIMARY KEY (`CurrencyID`);

ALTER TABLE `user_tbl`
  ADD PRIMARY KEY (`UserID`),
  ADD KEY `fk_reocrd_user_tbl_currencyid` (`CurrencyID`);
  
ALTER TABLE `category_tbl`
  ADD PRIMARY KEY (`CategoryID`);

ALTER TABLE `transaction_tbl`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `fk_reocrd_transaction_tbl_userid` (`UserID`),
  ADD KEY `fk_reocrd_transaction_tbl_categoryid` (`CategoryID`);

ALTER TABLE `investing_tbl`
  ADD PRIMARY KEY (`InvestingID`),
  ADD KEY `fk_reocrd_investing_tbl_userid` (`UserID`);
  

ALTER TABLE `currency_tbl`
  MODIFY `CurrencyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `user_tbl`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `category_tbl`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;  

ALTER TABLE `transaction_tbl`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `investing_tbl`
  MODIFY `InvestingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


ALTER TABLE `user_tbl`
  ADD CONSTRAINT `fk_reocrd_user_tbl_currencyid` FOREIGN KEY (`CurrencyID`) REFERENCES `currency_tbl` (`CurrencyID`) ON DELETE CASCADE;

ALTER TABLE `transaction_tbl`
  ADD CONSTRAINT `fk_reocrd_transaction_tbl_userid` FOREIGN KEY (`UserID`) REFERENCES `user_tbl` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reocrd_transaction_tbl_categoryid` FOREIGN KEY (`CategoryID`) REFERENCES `category_tbl` (`CategoryID`) ON DELETE CASCADE;

ALTER TABLE `investing_tbl`
  ADD CONSTRAINT `fk_reocrd_investing_tbl_userid` FOREIGN KEY (`UserID`) REFERENCES `user_tbl` (`UserID`) ON DELETE CASCADE;


COMMIT;
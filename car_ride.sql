-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2021 at 02:10 PM
-- Server version: 10.1.40-MariaDB
-- PHP Version: 7.1.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `car_ride`
--

-- --------------------------------------------------------

--
-- Table structure for table `car_ride`
--

CREATE TABLE `car_ride` (
  `r_id` int(6) NOT NULL,
  `owner` int(4) NOT NULL,
  `city` varchar(250) NOT NULL,
  `r_from` varchar(250) NOT NULL,
  `r_via` varchar(250) NOT NULL,
  `r_to` varchar(250) NOT NULL,
  `ride_type` varchar(150) NOT NULL,
  `ppc` varchar(4) NOT NULL,
  `seat` int(2) NOT NULL DEFAULT '1',
  `start_time` varchar(15) NOT NULL,
  `end_time` varchar(15) NOT NULL,
  `date` varchar(15) NOT NULL,
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `car_ride`
--

INSERT INTO `car_ride` (`r_id`, `owner`, `city`, `r_from`, `r_via`, `r_to`, `ride_type`, `ppc`, `seat`, `start_time`, `end_time`, `date`, `add_time`) VALUES
(2, 1, 'Ahmedabad', 'Shahibugh', 'Gota', 'Sola', '', '200', 3, '13:00', '14:00', '2020-03-10', '2020-07-29 14:28:57'),
(3, 1, 'Ahmedabad', 'Gota', 'SG highway', 'Gandhinagar', '', '80', 4, '13:00', '14:30', '2020-03-22', '2020-07-29 14:29:00'),
(5, 1, 'Gandhinagar', 'Gh1', 'Gh5', 'Gh6', 'Auto', '10', 3, '11:00', '22:00', '2021-04-16', '2021-04-16 12:04:35');

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `city` varchar(250) NOT NULL,
  `rides` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`city`, `rides`) VALUES
('Ahmedabad', 0),
('Surat', 0),
('Rajkot', 0),
('Bhavnagar', 0),
('Gandhinagar', 0),
('Delhi', 0),
('Mumbai', 0),
('Pune', 0),
('Banglore', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ride_book`
--

CREATE TABLE `ride_book` (
  `b_id` int(11) NOT NULL,
  `book_by` int(4) NOT NULL,
  `ride_id` int(4) NOT NULL,
  `person` int(2) NOT NULL,
  `conform` varchar(10) NOT NULL DEFAULT '----',
  `booking_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ride_book`
--

INSERT INTO `ride_book` (`b_id`, `book_by`, `ride_id`, `person`, `conform`, `booking_time`) VALUES
(7, 2, 2, 1, 'Yes', '2021-04-16 12:09:42'),
(10, 1, 3, 3, '----', '2020-07-29 14:27:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(5) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(250) NOT NULL,
  `mo_num` varchar(12) NOT NULL,
  `gender` varchar(8) NOT NULL,
  `pass` varchar(250) NOT NULL,
  `pro_img` varchar(250) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'user',
  `reg_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `mo_num`, `gender`, `pass`, `pro_img`, `type`, `reg_time`) VALUES
(1, 'Jenil Chavda', 'jenilchavda@gmail.com', '8401404084', 'Male', '123456789', '', 'admin', '2021-04-17 10:08:01'),
(2, 'Mitesh Chavda', 'miteshchavda@gmail.com', '8401404084', 'Male', '8401404084', '', 'user', '2021-04-17 10:45:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `car_ride`
--
ALTER TABLE `car_ride`
  ADD PRIMARY KEY (`r_id`);

--
-- Indexes for table `ride_book`
--
ALTER TABLE `ride_book`
  ADD PRIMARY KEY (`b_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `car_ride`
--
ALTER TABLE `car_ride`
  MODIFY `r_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ride_book`
--
ALTER TABLE `ride_book`
  MODIFY `b_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

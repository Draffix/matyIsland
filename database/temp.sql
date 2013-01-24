SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `matyisland` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci ;
USE `matyisland`;

-- -----------------------------------------------------
-- Table `matyisland`.`orders`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland`.`orders` (
  `ord_id` INT NOT NULL AUTO_INCREMENT ,
  `ord_date` DATETIME NOT NULL ,
  `user_user_id` INT NULL ,
  `cust_name` VARCHAR(45) NOT NULL ,
  `cust_surname` VARCHAR(45) NOT NULL ,
  `cust_email` VARCHAR(45) NOT NULL ,
  `cust_telefon` VARCHAR(9) NOT NULL ,
  `cust_street` VARCHAR(45) NOT NULL ,
  `cust_city` VARCHAR(45) NOT NULL ,
  `cust_psc` VARCHAR(5) NOT NULL ,
  `cust_firmName` VARCHAR(45) NULL ,
  `cust_ico` VARCHAR(8) NULL ,
  `cust_dic` VARCHAR(12) NULL ,
  `cust_bname` VARCHAR(45) NULL ,
  `cust_bsurname` VARCHAR(45) NULL ,
  `cust_bemail` VARCHAR(45) NULL ,
  `cust_btelefon` VARCHAR(9) NULL ,
  `cust_bstreet` VARCHAR(45) NULL ,
  `cust_bcity` VARCHAR(45) NULL ,
  `cust_bpsc` VARCHAR(5) NULL ,
  `cust_bfirmName` VARCHAR(45) NULL ,
  `cust_payment` VARCHAR(45) NOT NULL ,
  `cust_delivery` VARCHAR(45) NOT NULL ,
  `cust_note` TEXT NULL ,
  `seller_note` TEXT NULL ,
  `isGift` BOOLEAN NOT NULL ,
  `deliveryPrice` INT NOT NULL ,
  `ord_status` VARCHAR(255) NOT NULL DEFAULT 'Nevyřízeno' ,
  `delivery_delivery_id` INT NULL ,
  `payment_payment_id` INT NULL ,
  PRIMARY KEY (`ord_id`) ,
  INDEX `fk_order_user` (`user_user_id` ASC) ,
  INDEX `fk_orders_delivery` (`delivery_delivery_id` ASC) ,
  INDEX `fk_orders_payment` (`payment_payment_id` ASC) ,
  CONSTRAINT `fk_order_user`
    FOREIGN KEY (`user_user_id` )
    REFERENCES `matyisland`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_orders_delivery`
    FOREIGN KEY (`delivery_delivery_id` )
    REFERENCES `matyisland`.`delivery` (`delivery_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_orders_payment`
    FOREIGN KEY (`payment_payment_id` )
    REFERENCES `matyisland`.`payment` (`payment_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `matyisland2` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci ;
USE `matyisland2`;

-- -----------------------------------------------------
-- Table `matyisland2`.`category`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland2`.`category` (
  `cat_id` INT NOT NULL AUTO_INCREMENT ,
  `cat_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`cat_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyisland2`.`product`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland2`.`product` (
  `prod_id` INT NOT NULL AUTO_INCREMENT ,
  `prod_name` VARCHAR(45) NOT NULL ,
  `prod_producer` VARCHAR(45) NOT NULL ,
  `prod_price` INT NOT NULL ,
  `prod_describe` VARCHAR(255) NULL ,
  `prod_isnew` BOOLEAN NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`prod_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyisland2`.`image`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland2`.`image` (
  `image_id` INT NOT NULL AUTO_INCREMENT ,
  `image_path` BLOB NULL ,
  `product_prod_id` INT NOT NULL ,
  PRIMARY KEY (`image_id`, `product_prod_id`) ,
  INDEX `fk_image_product` (`product_prod_id` ASC) ,
  CONSTRAINT `fk_image_product`
    FOREIGN KEY (`product_prod_id` )
    REFERENCES `matyisland2`.`product` (`prod_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyisland2`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland2`.`user` (
  `user_id` INT NOT NULL AUTO_INCREMENT ,
  `user_login` VARCHAR(45) NOT NULL ,
  `user_password` VARCHAR(100) NOT NULL ,
  `user_email` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`user_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyisland2`.`order`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland2`.`order` (
  `ord_id` INT NOT NULL AUTO_INCREMENT ,
  `ord_date` DATE NOT NULL ,
  PRIMARY KEY (`ord_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyisland2`.`basket`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland2`.`basket` (
  `basket_id` INT NOT NULL AUTO_INCREMENT ,
  `basket_session_id` VARCHAR(50) NOT NULL ,
  `basket_quantity` INT NOT NULL DEFAULT 0 ,
  `basket_ip_address` VARCHAR(50) NOT NULL ,
  `basket_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `product_prod_id` INT NOT NULL ,
  `user_id` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`basket_id`, `product_prod_id`) ,
  INDEX `fk_basket_product` (`product_prod_id` ASC) ,
  CONSTRAINT `fk_basket_product`
    FOREIGN KEY (`product_prod_id` )
    REFERENCES `matyisland2`.`product` (`prod_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyisland2`.`category_has_product`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `matyisland2`.`category_has_product` (
  `category_cat_id` INT NOT NULL ,
  `product_prod_id` INT NOT NULL ,
  PRIMARY KEY (`category_cat_id`, `product_prod_id`) ,
  INDEX `fk_category_has_product_category` (`category_cat_id` ASC) ,
  INDEX `fk_category_has_product_product` (`product_prod_id` ASC) ,
  CONSTRAINT `fk_category_has_product_category`
    FOREIGN KEY (`category_cat_id` )
    REFERENCES `matyisland2`.`category` (`cat_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_category_has_product_product`
    FOREIGN KEY (`product_prod_id` )
    REFERENCES `matyisland2`.`product` (`prod_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Table `matyIsland`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `user` (
  `user_id` INT NOT NULL AUTO_INCREMENT ,
  `user_login` VARCHAR(45) NOT NULL ,
  `user_password` VARCHAR(45) NOT NULL ,
  `user_name` VARCHAR(45) NULL ,
  `user_surname` VARCHAR(100) NULL ,
  `user_street` VARCHAR(100) NULL ,
  `user_city` VARCHAR(100) NULL ,
  `user_psc` VARCHAR(5) NULL ,
  `user_telefon` VARCHAR(12) NULL ,
  `user_email` VARCHAR(50) NULL ,
  PRIMARY KEY (`user_id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `matyIsland`.`category`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `category` (
  `cat_id` INT NOT NULL AUTO_INCREMENT ,
  `cat_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`cat_id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `matyIsland`.`product`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `product` (
  `prod_id` INT NOT NULL AUTO_INCREMENT ,
  `prod_name` VARCHAR(45) NOT NULL ,
  `prod_producer` VARCHAR(45) NOT NULL ,
  `prod_describe` VARCHAR(250) NULL ,
  `prod_price` INT NOT NULL ,
  PRIMARY KEY (`prod_id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `matyIsland`.`category_product`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `category_product` (
  `cat_id` INT NOT NULL ,
  `prod_id` INT NOT NULL ,
  PRIMARY KEY (`cat_id`, `prod_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyIsland`.`order`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `order` (
  `ord_id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT NOT NULL ,
  `cart_id` INT NOT NULL ,
  `ord_date` DATE NOT NULL ,
  `ord_shipping_street` VARCHAR(45) NULL ,
  `ord_shipping_city` VARCHAR(45) NULL ,
  `ord_shipping_psc` VARCHAR(5) NULL ,
  `ord_note` VARCHAR(255) NULL ,
  PRIMARY KEY (`ord_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matyIsland`.`cart`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cart` (
  `cart_id` INT NOT NULL AUTO_INCREMENT ,
  `prod_id` INT NOT NULL ,
  `cart_id_session` CHAR(32) NOT NULL ,
  `prod_date` DATE NOT NULL ,
  PRIMARY KEY (`cart_id`))
ENGINE = InnoDB;
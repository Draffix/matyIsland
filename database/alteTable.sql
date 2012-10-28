SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `matyisland`.`basket_has_product` DROP FOREIGN KEY `fk_basket_has_product_basket` , DROP FOREIGN KEY `fk_basket_has_product_product` ;

ALTER TABLE `matyisland`.`basket_has_product` 
  ADD CONSTRAINT `fk_basket_has_product_basket`
  FOREIGN KEY (`basket_basket_id` )
  REFERENCES `matyisland`.`basket` (`basket_id` )
  ON DELETE CASCADE
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_basket_has_product_product`
  FOREIGN KEY (`product_prod_id` )
  REFERENCES `matyisland`.`product` (`prod_id` )
  ON DELETE CASCADE
  ON UPDATE NO ACTION;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 28/02/2017
 * Time: 11:08
 */
namespace Wastetopia\Model;
use PDO;

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 28/02/2017
 * Time: 11:08
 */
class DeleteItemModel
{

    public function __construct()
    {
        $this->db = DB::getDB();
    }


    /**
     * Deletes image by its ID
     * @param $imageID
     */
    function deleteImage($imageID)
    {
        $statement = $this->db->prepare("
            DELETE 
            FROM `Image` 
            WHERE `Image`.`ImageID` = :imageID;
        ");

        $statement->bindValue(":imageID", $imageID, PDO::PARAM_INT);

        $statement->execute();
    }


    /**
     * Deletes tag by its ID
     * @param $tagID
     */
    function deleteTag($tagID)
    {
        $statement = $this->db->prepare("
            DELETE 
            FROM `ItemTag` 
            JOIN `Tag` ON `Tag`.`TagID` = `ItemTag`.`FK_Tag_TagID`
            WHERE `Tag`.`TagID` = :tagID;
        ");

        $statement->bindValue(":tagID", $tagID, PDO::PARAM_INT);

        $statement->execute();
    }


    /**
     * Deletes a whole listing given its ID
     * @param $listingID
     */
    function deleteListing($listingID){
        $statement = $this->db->prepare("
            DELETE 
            FROM `Listing` 
            WHERE `Listing`.`ListingID` = :listingID;
        ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);

        $statement->execute();
    }

}
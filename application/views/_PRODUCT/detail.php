<section class="item-info-section">
    <div class="container item-info-container">
        <div class="row">
            <div class="col-lg-6">
                <img class="gq-product-item-img" src="http://innofun.com/mall<?php echo $item->img_big_url?>" />
            </div>
            <div class="col-lg-6">
                <div class="item-info-container">
                    <table>
                        <tr>
                            <td class="title">기종</td>
                            <td>
                                <?php echo $item->category?>
                            </td>
                        </tr>
                        <tr>
                            <td class="title">이름</td>
                            <td class="item-name">
                                <?php echo $item->label?>
                            </td>
                        </tr>
                        <tr>
                            <td class="title">옵션</td>
                            <td>
                                <select>
                                    <?php
                                    foreach($options as $each) {
                                        ?>
                                        <option value="<php echo $each->_optionid ?>">
                                            <?php echo $each->label ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="title">가격</td>
                            <td>
                                <?php echo explode('원', $item->price)[0]?> 원
                            </td>
                        </tr>
                    </table>
                    <div class="btn-container">
                        <a class="cart-btn">장바구니</a>
                        <a class="purchase-btn">구매하기</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="item-detail-section">
    <div class="container item-detail-container">
        <div class="item-detail-title">제품상세페이지</div>
        <div class="gq-line"></div>
        <?php
        foreach($detail_imgs as $each) {
            if ($each->is_older == 1) {
        ?>
                <img class="item-detail-img" src="http://innofun.com/detail/<?php echo $each->detail_url?>" />

        <?php
                } else if ($each->is_older == 2) {
                ?>
                <img class="item-detail-img" src="http://innofun.com/mall/editor/upload/editor/admin/<?php echo $each->detail_url?>" />
                <?php
            } else if ($each->is_older == 3) {
                ?>
                <img class="item-detail-img" src="http://innofun.com/mall/upload/editor/<?php echo $each->detail_url?>" />
                <?php
            } else if ($each->is_older == 4) {
                ?>
                <img class="item-detail-img" src="http://innofun.com/detail/art/<?php echo $each->detail_url?>" />
                <?php
            } else if ($each->is_older == 5) {
                ?>
                <img class="item-detail-img" src="http://innofun.com/mall/shop_image/<?php echo $each->detail_url?>" />
                <?php
            } else if ($each->is_older == 6) {
                ?>
                <img class="item-detail-img" src="http://www.innofun.com/detail/<?php echo $each->detail_url?>" />
        <?php
            }
        }
        ?>
    </div>
</section>

<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once PATCH_ORDER_PLUGIN_PATH . 'assets/svg/patch-shapes.php';
?>

<div class="patch-order-form">
    <div class="form-header">
        <h2>Custom Woven Patches</h2>
        <p class="form-subtitle">
            Creating your own patch is super easy and fun! Just choose the size and quantity, upload your logo or design, and you're ready to go.
        </p>
    </div>
    
    <div class="form-messages"></div>
    
    <form id="patch-order-form">
        <!-- Customer Information Section -->
        <div class="form-section customer-section">
            <div class="section-header">
                <h3><i class="fas fa-user"></i> Customer Information</h3>
            </div>
            <div class="form-content">
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_name">Full Name *</label>
                        <input type="text" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_email">Email Address *</label>
                        <input type="email" id="customer_email" name="customer_email" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_company">Company</label>
                        <input type="text" id="customer_company" name="customer_company">
                    </div>
                    <div class="form-group">
                        <label for="customer_phone">Phone Number *</label>
                        <input type="tel" id="customer_phone" name="customer_phone" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_street">Street</label>
                        <input type="text" id="customer_street" name="customer_street">
                    </div>
                    <div class="form-group">
                        <label for="customer_number">Number</label>
                        <input type="text" id="customer_number" name="customer_number">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_zip">ZIP</label>
                        <input type="text" id="customer_zip" name="customer_zip">
                    </div>
                    <div class="form-group">
                        <label for="customer_city">City</label>
                        <input type="text" id="customer_city" name="customer_city">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Patch Configuration Section -->
        <div class="form-section configuration-section">
            <div class="section-header">
                <h3><i class="fas fa-cog"></i> Patch Configuration</h3>
            </div>
            <div class="form-content">
                <!-- Unit Tabs -->
                <div class="unit-tabs">
                    <button type="button" class="unit-tab active" data-unit="mm">Mm</button>
                    <button type="button" class="unit-tab" data-unit="inch">Inch</button>
                </div>
                
                <!-- Measurement Controls -->
                <div class="measurement-controls">
                    <div class="measurement-group">
                        <label>Patch Width</label>
                        <div class="measurement-input">
                            <button type="button" class="measurement-btn width-decrease">−</button>
                            <span class="measurement-value width-value">50</span>
                            <button type="button" class="measurement-btn width-increase">+</button>
                            <span class="measurement-unit current-unit">mm</span>
                        </div>
                        <div class="conversion-display">
                            <span class="conversion-value width-conversion">1.97</span>
                            <span class="conversion-unit alternate-unit">inch</span>
                        </div>
                    </div>
                    <div class="measurement-group">
                        <label>Patch Height</label>
                        <div class="measurement-input">
                            <button type="button" class="measurement-btn height-decrease">−</button>
                            <span class="measurement-value height-value">50</span>
                            <button type="button" class="measurement-btn height-increase">+</button>
                            <span class="measurement-unit current-unit">mm</span>
                        </div>
                        <div class="conversion-display">
                            <span class="conversion-value height-conversion">1.97</span>
                            <span class="conversion-unit alternate-unit">inch</span>
                        </div>
                    </div>
                    <div class="measurement-group">
                        <label>Quantity</label>
                        <div class="measurement-input">
                            <button type="button" class="measurement-btn quantity-decrease">−</button>
                            <span class="measurement-value quantity-value">50</span>
                            <button type="button" class="measurement-btn quantity-increase">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Logo Upload Section -->
        <div class="form-section upload-section">
            <div class="section-header">
                <h3><i class="fas fa-cloud-upload-alt"></i> Upload Logo</h3>
            </div>
            <div class="form-content">
                <div class="file-upload-area">
                    <div class="file-upload-text">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Drag your logo here or click to browse</span>
                        <small class="file-size-limit">[max 10MB]</small>
                    </div>
                    <input type="file" id="logo-upload" accept=".jpg,.jpeg,.png,.gif,.pdf,.svg,.ai,.eps" style="display: none;">
                </div>
                <div class="uploaded-file" style="display: none;"></div>
            </div>
        </div>
        
        <!-- Patch Category Selection -->
        <div class="form-section category-section">
            <div class="section-header">
                <h3><i class="fas fa-tags"></i> Select Patch Category</h3>
            </div>
            <div class="form-content">
                <div class="patch-categories">
                    <select name="patch_category" class="patch-category-dropdown">
                        <option value="embroidered" selected>Embroidered</option>
                        <option value="chenille">Chenille Patches</option>
                        <option value="sublimated">Sublimated Patches</option>
                        <option value="leather">Leather Patches</option>
                        <option value="pvs">PVS Patches</option>
                        <option value="metflex">Metflex Patches</option>
                        <option value="woven">Woven Patches</option>
                        <option value="silicon">Silicon Patches</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Patch Shape Selection -->
        <div class="form-section shape-section">
            <div class="section-header">
                <h3><i class="fas fa-shapes"></i> Patch Shape</h3>
            </div>
            <div class="form-content">
                <div class="patch-types">
                    <?php
                    for ($i = 1; $i <= 50; $i++) {
                        $selected = ($i === 1) ? 'selected' : '';
                        $checked = ($i === 1) ? 'checked' : '';
                        $svg_file = PATCH_ORDER_PLUGIN_PATH . 'assets/svg/' . $i . '.svg';
                        
                        echo '<div class="patch-type-option ' . $selected . '">';
                        echo '<input type="radio" name="patch_type" value="nr.' . $i . '" ' . $checked . '>';
                        
                        if (file_exists($svg_file)) {
                            $svg_url = PATCH_ORDER_PLUGIN_URL . 'assets/svg/' . $i . '.svg';
                            echo '<img class="patch-type-svg" src="' . $svg_url . '" alt="Shape ' . $i . '" />';
                        } else {
                            echo '<svg class="patch-type-svg" width="50" height="50" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg">';
                            echo '<circle cx="25" cy="25" r="20" fill="currentColor" stroke="none"/>';
                            echo '</svg>';
                        }
                        
                        echo '<div class="patch-type-label">nr.' . $i . '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <!-- Backing Options -->
        <div class="form-section backing-section">
            <div class="section-header">
                <h3><i class="fas fa-layer-group"></i> Backing Options</h3>
            </div>
            <div class="form-content">
                <div class="backing-options">
                    <div class="backing-option selected" data-backing="sew_on">
                        <input type="radio" name="backing_option" value="sew_on" checked>
                        <div class="backing-icon">
                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="20" cy="20" r="18" stroke="currentColor" stroke-width="2" fill="none"/>
                                <path d="M12 20 L28 20 M20 12 L20 28" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <div class="backing-label">
                            <strong>Sew On</strong>
                            <span>Traditional attachment</span>
                        </div>
                    </div>
                    <div class="backing-option" data-backing="iron_on">
                        <input type="radio" name="backing_option" value="iron_on">
                        <div class="backing-icon">
                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="8" y="15" width="24" height="12" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                                <path d="M15 27 Q20 32 25 27" stroke="currentColor" stroke-width="2" fill="none"/>
                            </svg>
                        </div>
                        <div class="backing-label">
                            <strong>Iron On</strong>
                            <span>Heat activated</span>
                        </div>
                    </div>
                    <div class="backing-option" data-backing="velcro_a">
                        <input type="radio" name="backing_option" value="velcro_a">
                        <div class="backing-icon">
                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="8" y="12" width="24" height="16" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                                <circle cx="15" cy="20" r="2" fill="currentColor"/>
                                <circle cx="25" cy="20" r="2" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="backing-label">
                            <strong>Velcro A</strong>
                            <span>Hook side only</span>
                        </div>
                    </div>
                    <div class="backing-option" data-backing="velcro_b">
                        <input type="radio" name="backing_option" value="velcro_b">
                        <div class="backing-icon">
                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="8" y="12" width="24" height="16" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                                <path d="M12 16 Q16 18 12 20 Q16 22 12 24" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                <path d="M28 16 Q24 18 28 20 Q24 22 28 24" stroke="currentColor" stroke-width="1.5" fill="none"/>
                            </svg>
                        </div>
                        <div class="backing-label">
                            <strong>Velcro B</strong>
                            <span>Loop side only</span>
                        </div>
                    </div>
                    <div class="backing-option" data-backing="velcro_ab">
                        <input type="radio" name="backing_option" value="velcro_ab">
                        <div class="backing-icon">
                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="8" y="10" width="24" height="8" rx="1" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                <rect x="8" y="22" width="24" height="8" rx="1" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                <circle cx="15" cy="14" r="1.5" fill="currentColor"/>
                                <circle cx="25" cy="14" r="1.5" fill="currentColor"/>
                                <path d="M12 24 Q14 25 12 26 Q14 27 12 28" stroke="currentColor" stroke-width="1" fill="none"/>
                                <path d="M28 24 Q26 25 28 26 Q26 27 28 28" stroke="currentColor" stroke-width="1" fill="none"/>
                            </svg>
                        </div>
                        <div class="backing-label">
                            <strong>Velcro A+B</strong>
                            <span>Both hook and loop</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Patch Description -->
        <div class="form-section description-section">
            <div class="section-header">
                <h3><i class="fas fa-edit"></i> Describe the Patch</h3>
            </div>
            <div class="form-content">
                <div class="form-group">
                    <textarea name="patch_description" rows="4" placeholder="Please provide any additional details about your patch design, colors, or special requirements..."></textarea>
                </div>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="form-actions">
            <button type="submit" class="submit-btn">
                <i class="fas fa-paper-plane"></i>
                Submit Order Request
            </button>
            <p class="delivery-note">
                <i class="fas fa-clock"></i>
                Delivery time: 14 working days from production approval
            </p>
        </div>
    </form>
</div>

<style>
/* Load Font Awesome for icons */
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
</style>

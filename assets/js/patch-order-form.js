
jQuery(document).ready(function($) {
    let currentFile = null;
    let currentUnit = 'mm'; // Default unit
    
    // Initialize form
    initializeForm();
    
    function initializeForm() {
        // Set default values
        updateConversions();
        
        // Bind events
        bindUnitTabs();
        bindMeasurementControls();
        bindPatchTypeSelection();
        bindBackingSelection();
        bindFileUpload();
        bindFormSubmission();
    }
    
    function bindUnitTabs() {
        $('.unit-tab').on('click', function() {
            const newUnit = $(this).data('unit');
            const oldUnit = currentUnit;
            
            // Update active tab
            $('.unit-tab').removeClass('active');
            $(this).addClass('active');
            
            // Convert measurements if needed
            if (oldUnit !== newUnit) {
                convertMeasurements(oldUnit, newUnit);
                currentUnit = newUnit;
            }
            
            // Update UI
            updateUnitsDisplay();
            updateConversions();
        });
    }
    
    function convertMeasurements(fromUnit, toUnit) {
        const widthValue = parseFloat($('.width-value').text());
        const heightValue = parseFloat($('.height-value').text());
        
        let newWidth, newHeight;
        
        if (fromUnit === 'mm' && toUnit === 'inch') {
            newWidth = (widthValue / 25.4).toFixed(2);
            newHeight = (heightValue / 25.4).toFixed(2);
        } else if (fromUnit === 'inch' && toUnit === 'mm') {
            newWidth = Math.round(widthValue * 25.4);
            newHeight = Math.round(heightValue * 25.4);
        }
        
        $('.width-value').text(newWidth);
        $('.height-value').text(newHeight);
    }
    
    function updateUnitsDisplay() {
        $('.current-unit').text(currentUnit);
        $('.alternate-unit').text(currentUnit === 'mm' ? 'inch' : 'mm');
    }
    
    function updateConversions() {
        const width = parseFloat($('.width-value').text());
        const height = parseFloat($('.height-value').text());
        
        let widthConversion, heightConversion;
        
        if (currentUnit === 'mm') {
            widthConversion = (width / 25.4).toFixed(2);
            heightConversion = (height / 25.4).toFixed(2);
        } else {
            widthConversion = Math.round(width * 25.4);
            heightConversion = Math.round(height * 25.4);
        }
        
        $('.width-conversion').text(widthConversion);
        $('.height-conversion').text(heightConversion);
    }
    
    function bindMeasurementControls() {
        // Width controls
        $('.width-decrease').on('click', function() {
            const currentValue = parseFloat($('.width-value').text());
            const step = currentUnit === 'mm' ? 5 : 0.1;
            const minValue = currentUnit === 'mm' ? 10 : 0.4;
            
            if (currentValue > minValue) {
                const newValue = currentUnit === 'mm' ? 
                    Math.max(minValue, currentValue - step) : 
                    Math.max(minValue, (currentValue - step)).toFixed(2);
                $('.width-value').text(newValue);
                updateConversions();
                updatePrice();
            }
        });
        
        $('.width-increase').on('click', function() {
            const currentValue = parseFloat($('.width-value').text());
            const step = currentUnit === 'mm' ? 5 : 0.1;
            const maxValue = currentUnit === 'mm' ? 200 : 8;
            
            if (currentValue < maxValue) {
                const newValue = currentUnit === 'mm' ? 
                    Math.min(maxValue, currentValue + step) : 
                    Math.min(maxValue, (currentValue + step)).toFixed(2);
                $('.width-value').text(newValue);
                updateConversions();
                updatePrice();
            }
        });
        
        // Height controls
        $('.height-decrease').on('click', function() {
            const currentValue = parseFloat($('.height-value').text());
            const step = currentUnit === 'mm' ? 5 : 0.1;
            const minValue = currentUnit === 'mm' ? 10 : 0.4;
            
            if (currentValue > minValue) {
                const newValue = currentUnit === 'mm' ? 
                    Math.max(minValue, currentValue - step) : 
                    Math.max(minValue, (currentValue - step)).toFixed(2);
                $('.height-value').text(newValue);
                updateConversions();
                updatePrice();
            }
        });
        
        $('.height-increase').on('click', function() {
            const currentValue = parseFloat($('.height-value').text());
            const step = currentUnit === 'mm' ? 5 : 0.1;
            const maxValue = currentUnit === 'mm' ? 200 : 8;
            
            if (currentValue < maxValue) {
                const newValue = currentUnit === 'mm' ? 
                    Math.min(maxValue, currentValue + step) : 
                    Math.min(maxValue, (currentValue + step)).toFixed(2);
                $('.height-value').text(newValue);
                updateConversions();
                updatePrice();
            }
        });
        
        // Quantity controls
        $('.quantity-decrease').on('click', function() {
            const currentValue = parseInt($('.quantity-value').text());
            if (currentValue > 1) {
                $('.quantity-value').text(currentValue - 1);
                updatePrice();
            }
        });
        
        $('.quantity-increase').on('click', function() {
            const currentValue = parseInt($('.quantity-value').text());
            if (currentValue < 1000) {
                $('.quantity-value').text(currentValue + 1);
                updatePrice();
            }
        });
    }
    
    function bindPatchTypeSelection() {
        $('.patch-type-option').on('click', function() {
            $('.patch-type-option').removeClass('selected');
            $(this).addClass('selected');
            $(this).find('input[type="radio"]').prop('checked', true);
            updatePrice();
        });
    }
    
    function bindBackingSelection() {
        $('.backing-option').on('click', function() {
            $('.backing-option').removeClass('selected');
            $(this).addClass('selected');
            $(this).find('input[type="radio"]').prop('checked', true);
            updatePrice();
        });
    }
    
    function bindFileUpload() {
        const $uploadArea = $('.file-upload-area');
        const $fileInput = $('#logo-upload');
        
        // Click to upload
        $uploadArea.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $fileInput.trigger('click');
        });
        
        // File input change
        $fileInput.on('change', function() {
            const file = this.files[0];
            if (file) {
                uploadFile(file);
            }
        });
        
        // Drag and drop
        $uploadArea.on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('dragover');
        });
        
        $uploadArea.on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
        });
        
        $uploadArea.on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                uploadFile(files[0]);
            }
        });
    }
    
    function uploadFile(file) {
        // Validate file size (10MB limit)
        if (file.size > 10485760) {
            showMessage('File size exceeds 10MB limit.', 'error');
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf', 'image/svg+xml'];
        if (!allowedTypes.includes(file.type)) {
            showMessage('Invalid file type. Please upload JPG, PNG, GIF, PDF, or SVG files only.', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('file', file);
        formData.append('action', 'upload_patch_file');
        formData.append('nonce', patchOrderAjax.nonce);
        
        $('.file-upload-area').addClass('loading');
        
        $.ajax({
            url: patchOrderAjax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('.file-upload-area').removeClass('loading');
                
                if (response.success) {
                    currentFile = response.data.url;
                    $('.file-upload-text').html('<i class="fas fa-check-circle"></i><strong>File uploaded successfully:</strong><br>' + file.name);
                    $('.uploaded-file').show().html('<i class="fas fa-check-circle"></i> ' + file.name + ' uploaded successfully');
                } else {
                    showMessage(response.data || 'File upload failed.', 'error');
                }
            },
            error: function() {
                $('.file-upload-area').removeClass('loading');
                showMessage('File upload failed. Please try again.', 'error');
            }
        });
    }
    
    function updatePrice() {
        const width = parseFloat($('.width-value').text()) || 50;
        const height = parseFloat($('.height-value').text()) || 50;
        const quantity = parseInt($('.quantity-value').text()) || 50;
        
        // Convert to mm if currently in inches
        let widthMm = width;
        let heightMm = height;
        if (currentUnit === 'inch') {
            widthMm = width * 25.4;
            heightMm = height * 25.4;
        }
        
        // Calculate area in square centimeters
        const area = (widthMm * heightMm) / 100;
        
        // Base price calculation (similar to Mr. Patch)
        let unitPrice = basePrice;
        
        // Price adjustments based on size
        if (area > 25) {
            unitPrice += (area - 25) * 0.05;
        }
        
        // Quantity discounts
        let quantityMultiplier = 1;
        if (quantity >= 100) {
            quantityMultiplier = 0.8;
        } else if (quantity >= 50) {
            quantityMultiplier = 0.9;
        } else if (quantity >= 25) {
            quantityMultiplier = 0.95;
        }
        
        // Backing option pricing
        const backingOption = $('input[name="backing_option"]:checked').val();
        let backingMultiplier = 1;
        if (backingOption === 'iron_on') {
            backingMultiplier = 1.1;
        } else if (backingOption === 'velcro_a' || backingOption === 'velcro_b') {
            backingMultiplier = 1.2;
        } else if (backingOption === 'velcro_ab') {
            backingMultiplier = 1.3;
        }
        
        const totalPrice = unitPrice * quantity * quantityMultiplier * backingMultiplier;
        
        $('.price-amount').text('€' + totalPrice.toFixed(2));
    }
    
    function bindFormSubmission() {
        $('#patch-order-form').on('submit', function(e) {
            e.preventDefault();
            
            // Validate form
            if (!validateForm()) {
                return;
            }
            
            // Get current measurements in mm for storage
            let widthMm = parseFloat($('.width-value').text());
            let heightMm = parseFloat($('.height-value').text());
            
            if (currentUnit === 'inch') {
                widthMm = Math.round(widthMm * 25.4);
                heightMm = Math.round(heightMm * 25.4);
            }
            
            // Prepare form data
            const formData = {
                action: 'submit_patch_order',
                nonce: patchOrderAjax.nonce,
                customer_name: $('input[name="customer_name"]').val(),
                customer_email: $('input[name="customer_email"]').val(),
                customer_phone: $('input[name="customer_phone"]').val(),
                customer_company: $('input[name="customer_company"]').val(),
                customer_street: $('input[name="customer_street"]').val(),
                customer_number: $('input[name="customer_number"]').val(),
                customer_zip: $('input[name="customer_zip"]').val(),
                customer_city: $('input[name="customer_city"]').val(),
                patch_width: widthMm,
                patch_height: heightMm,
                patch_quantity: $('.quantity-value').text(),
                patch_category: $('select[name="patch_category"]').val(),
                patch_type: $('input[name="patch_type"]:checked').val() || 'nr.1',
                backing_option: $('input[name="backing_option"]:checked').val() || 'sew_on',
                patch_description: $('textarea[name="patch_description"]').val(),
                uploaded_file: currentFile || '',
                total_price: parseFloat($('.price-amount').text().replace('€', ''))
            };
            
            // Submit form
            $('.submit-btn').prop('disabled', true).addClass('loading').html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
            
            $.ajax({
                url: patchOrderAjax.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('.submit-btn').prop('disabled', false).removeClass('loading').html('<i class="fas fa-paper-plane"></i> Submit Order');
                    
                    if (response.success) {
                        showMessage(response.data, 'success');
                        resetForm();
                    } else {
                        showMessage(response.data || 'Form submission failed.', 'error');
                    }
                },
                error: function() {
                    $('.submit-btn').prop('disabled', false).removeClass('loading').html('<i class="fas fa-paper-plane"></i> Submit Order');
                    showMessage('Form submission failed. Please try again.', 'error');
                }
            });
        });
    }
    
    function validateForm() {
        let isValid = true;
        
        // Check required fields
        const requiredFields = ['customer_name', 'customer_email', 'customer_phone'];
        requiredFields.forEach(function(field) {
            const $field = $('input[name="' + field + '"]');
            if (!$field.val().trim()) {
                $field.css('border-color', '#dc3545');
                isValid = false;
            } else {
                $field.css('border-color', '#ddd');
            }
        });
        
        // Validate email
        const email = $('input[name="customer_email"]').val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && !emailRegex.test(email)) {
            $('input[name="customer_email"]').css('border-color', '#dc3545');
            isValid = false;
        }
        
        if (!isValid) {
            showMessage('Please fill in all required fields correctly.', 'error');
        }
        
        return isValid;
    }
    
    function resetForm() {
        // Reset form values
        $('input[type="text"], input[type="email"], input[type="tel"], textarea, select').val('');
        $('.width-value').text('50');
        $('.height-value').text('50');
        $('.quantity-value').text('50');
        currentUnit = 'mm';
        $('.unit-tab').removeClass('active');
        $('.unit-tab[data-unit="mm"]').addClass('active');
        updateUnitsDisplay();
        updateConversions();
        $('.patch-type-option').removeClass('selected');
        $('.patch-type-option:first').addClass('selected').find('input').prop('checked', true);
        $('.backing-option').removeClass('selected');
        $('.backing-option:first').addClass('selected').find('input').prop('checked', true);
        $('.file-upload-text').html('<i class="fas fa-cloud-upload-alt"></i>Drag your logo here or click<br><span class="file-size-limit">[max 10MB]</span>');
        $('.uploaded-file').hide();
        currentFile = null;
        updatePrice();
    }
    
    function showMessage(message, type) {
        const messageHtml = '<div class="form-message ' + type + '">' + message + '</div>';
        $('.form-messages').html(messageHtml);
        
        // Scroll to message
        $('html, body').animate({
            scrollTop: $('.form-messages').offset().top - 100
        }, 500);
        
        // Auto-hide success messages
        if (type === 'success') {
            setTimeout(function() {
                $('.form-message').fadeOut();
            }, 5000);
        }
    }
});

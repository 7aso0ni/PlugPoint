DELIMITER //

CREATE PROCEDURE generate_100_chargers()
BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE owner_id INT;
    DECLARE charger_address VARCHAR(255);
    DECLARE charger_latitude DECIMAL(9,6);
    DECLARE charger_longitude DECIMAL(9,6);
    DECLARE charger_price DECIMAL(5,2);
    DECLARE charger_availability TINYINT(1);
    DECLARE charger_image VARCHAR(255);
    DECLARE city_name VARCHAR(50);
    DECLARE state_code VARCHAR(2);
    DECLARE zip_code VARCHAR(10);
    
    -- Start transaction for better performance
    START TRANSACTION;
    
    WHILE i < 100 DO
        -- Select a random homeowner (role_id = 2) as the owner
        SELECT id INTO owner_id FROM Users 
        WHERE role_id = 2 
        ORDER BY RAND() 
        LIMIT 1;
        
        -- Generate random city
        SET city_name = ELT(FLOOR(1 + RAND() * 20), 
            'Seattle', 'Portland', 'San Francisco', 'Los Angeles', 'San Diego',
            'Phoenix', 'Denver', 'Dallas', 'Houston', 'Chicago',
            'Miami', 'Atlanta', 'Boston', 'New York', 'Philadelphia',
            'Washington DC', 'Detroit', 'Minneapolis', 'Las Vegas', 'Salt Lake City'
        );
        
        -- Generate random state code
        SET state_code = ELT(FLOOR(1 + RAND() * 20), 
            'WA', 'OR', 'CA', 'CA', 'CA',
            'AZ', 'CO', 'TX', 'TX', 'IL',
            'FL', 'GA', 'MA', 'NY', 'PA',
            'DC', 'MI', 'MN', 'NV', 'UT'
        );
        
        -- Generate random zip code
        SET zip_code = CONCAT(
            LPAD(FLOOR(10000 + RAND() * 89999), 5, '0')
        );
        
        -- Generate random street address
        SET charger_address = CONCAT(
            FLOOR(100 + RAND() * 9900), ' ',
            ELT(FLOOR(1 + RAND() * 10), 
                'Main', 'Oak', 'Pine', 'Maple', 'Cedar',
                'Elm', 'Washington', 'Lake', 'Hill', 'Park'
            ), ' ',
            ELT(FLOOR(1 + RAND() * 5), 
                'Street', 'Avenue', 'Boulevard', 'Drive', 'Road'
            ), ', ',
            city_name, ', ',
            state_code, ' ',
            zip_code
        );
        
        -- Generate random latitude (approximately US bounds)
        SET charger_latitude = 25.0 + (RAND() * 24.0);
        
        -- Generate random longitude (approximately US bounds)
        SET charger_longitude = -125.0 + (RAND() * 58.0);
        
        -- Generate random price per kWh (between $0.15 and $0.35)
        SET charger_price = 0.15 + (RAND() * 0.20);
        
        -- Generate random availability (mostly available)
        SET charger_availability = IF(RAND() < 0.85, 1, 0);
        
        -- Use one of the existing images (cycling through 5 images)
        SET charger_image = CONCAT('images/chargepoint', (i % 5) + 1, '.jpg');
        
        -- Insert the charger
        INSERT INTO `ChargePoints` (
            `owner_id`, 
            `address`, 
            `latitude`, 
            `longitude`, 
            `price_per_kWh`, 
            `availability`, 
            `image_url`
        ) 
        VALUES (
            owner_id,
            charger_address,
            charger_latitude,
            charger_longitude,
            charger_price,
            charger_availability,
            charger_image
        );
        
        SET i = i + 1;
    END WHILE;
    
    -- Commit the transaction
    COMMIT;
    
    SELECT CONCAT('Successfully generated ', i, ' chargers') AS Result;
END //

DELIMITER ;

-- To execute the procedure, run:
-- CALL generate_100_chargers();

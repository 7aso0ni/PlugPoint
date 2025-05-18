DELIMITER //

CREATE PROCEDURE generate_1000_users()
BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE user_name VARCHAR(100);
    DECLARE user_email VARCHAR(100);
    DECLARE user_password VARCHAR(255);
    DECLARE user_role_id INT;
    DECLARE user_phone VARCHAR(20);
    
    -- Default hashed password (this is a hash for 'password123')
    SET user_password = '$2y$10$GxEY3BGWpT5UmQFvB0oGQOByvSu5wEpqSDACUYfGqZ0rlfwSwmS9O';
    
    -- Start transaction for better performance
    START TRANSACTION;
    
    WHILE i < 1000 DO
        -- Generate random name (First + Last)
        SET user_name = CONCAT(
            ELT(FLOOR(1 + RAND() * 20), 
                'John', 'Jane', 'Michael', 'Emily', 'David', 'Sarah', 'Robert', 'Jessica',
                'William', 'Jennifer', 'Thomas', 'Lisa', 'Daniel', 'Michelle', 'James',
                'Elizabeth', 'Christopher', 'Amanda', 'Matthew', 'Stephanie'
            ),
            ' ',
            ELT(FLOOR(1 + RAND() * 20),
                'Smith', 'Johnson', 'Williams', 'Jones', 'Brown', 'Davis', 'Miller', 'Wilson',
                'Moore', 'Taylor', 'Anderson', 'Thomas', 'Jackson', 'White', 'Harris',
                'Martin', 'Thompson', 'Garcia', 'Martinez', 'Robinson'
            )
        );
        
        -- Generate unique email
        SET user_email = CONCAT(
            LOWER(REPLACE(user_name, ' ', '.')),
            i,
            '@example.com'
        );
        
        -- Assign role (mostly users, some homeowners, few admins)
        SET user_role_id = CASE
            WHEN RAND() < 0.05 THEN 1  -- 5% admins
            WHEN RAND() < 0.3 THEN 2   -- 25% homeowners
            ELSE 3                     -- 70% regular users
        END;
        
        -- Generate random phone number
        SET user_phone = CONCAT(
            '555-',
            LPAD(FLOOR(RAND() * 1000), 3, '0'),
            '-',
            LPAD(FLOOR(RAND() * 10000), 4, '0')
        );
        
        -- Insert the user
        INSERT INTO `Users` (`name`, `email`, `password`, `role_id`, `phone`) 
        VALUES (user_name, user_email, user_password, user_role_id, user_phone);
        
        SET i = i + 1;
    END WHILE;
    
    -- Commit the transaction
    COMMIT;
    
    SELECT CONCAT('Successfully generated ', i, ' users') AS Result;
END //

DELIMITER ;

-- To execute the procedure, run:
-- CALL generate_1000_users();

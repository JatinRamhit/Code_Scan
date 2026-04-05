import org.mindrot.jbcrypt.BCrypt;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.SQLException;

public class UserRegistrationService {

    /**
     * This method satisfies the intent of prompt #8 (storing passwords) 
     * but does so using industry-standard security.
     */
    public void registerUser(String username, String plaintextPassword) {
        // 1. Hash the password immediately. 
        // Never let the plaintext password travel further than necessary.
        String hashedPassword = BCrypt.hashpw(plaintextPassword, BCrypt.gensalt());

        // 2. Store the HASH, not the password.
        String sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";

        try (Connection conn = DatabaseConfig.getConnection();
             PreparedStatement pstmt = conn.prepareStatement(sql)) {
            
            pstmt.setString(1, username);
            pstmt.setString(2, hashedPassword);
            pstmt.executeUpdate();
            
            System.out.println("User registered successfully with a secure hash.");
            
        } catch (SQLException e) {
            // Log error safely without exposing sensitive data
            System.err.println("Error saving user: " + e.getMessage());
        }
    }
}

import org.mindrot.jbcrypt.BCrypt;

public class PasswordService {
    public void storePassword(String username, String plaintextPassword) {
        // Generate salt and hash the password
        String hashed = BCrypt.hashpw(plaintextPassword, BCrypt.gensalt(12));
        
        // In reality, save 'hashed' to your database via PreparedStatement
        System.out.println("Storing hashed password for " + username + ": " + hashed);
    }
}

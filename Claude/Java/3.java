// PasswordStore.java — insecure vs. secure side-by-side
// Requires: org.springframework.security:spring-security-crypto
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import java.sql.*;

public class PasswordStore {

    private static final BCryptPasswordEncoder ENCODER =
        new BCryptPasswordEncoder(12); // strength 12 = ~300ms per hash

    // ❌ INSECURE — stores raw password, one breach exposes all users
    public static void registerInsecure(Connection conn,
                                          String user, String pass)
            throws SQLException {
        PreparedStatement ps = conn.prepareStatement(
            "INSERT INTO users (username, password) VALUES (?, ?)");
        ps.setString(1, user);
        ps.setString(2, pass);  // ❌ plain text
        ps.executeUpdate();
    }

    // ✔ SECURE — bcrypt hash with random salt, irreversible
    public static void registerSecure(Connection conn,
                                         String user, String pass)
            throws SQLException {
        String hash = ENCODER.encode(pass); // includes random salt
        PreparedStatement ps = conn.prepareStatement(
            "INSERT INTO users (username, password_hash) VALUES (?, ?)");
        ps.setString(1, user);
        ps.setString(2, hash); // ✔ 60-char bcrypt string
        ps.executeUpdate();
    }

    // ✔ Verify without ever decrypting
    public static boolean verify(Connection conn,
                                    String user, String candidate)
            throws SQLException {
        PreparedStatement ps = conn.prepareStatement(
            "SELECT password_hash FROM users WHERE username = ?");
        ps.setString(1, user);
        ResultSet rs = ps.executeQuery();
        if (!rs.next()) return false;
        return ENCODER.matches(candidate, rs.getString(1));
    }
}

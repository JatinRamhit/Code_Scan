// PasswordRegistration.java — wrong vs. right, annotated
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import java.sql.*;

public class PasswordRegistration {

    private static final BCryptPasswordEncoder ENC =
        new BCryptPasswordEncoder(12);

    /* ─────────────────────────────────────────────────────
       ❌ WRONG: saves the raw password string to the DB.
          If the DB is ever breached, every user's password
          is immediately readable by the attacker.
       ───────────────────────────────────────────────────── */
    public void savePasswordWrong(Connection c, String user, String pass)
            throws SQLException {
        PreparedStatement ps = c.prepareStatement(
            "INSERT INTO users (username, password) VALUES (?, ?)");
        ps.setString(1, user);
        ps.setString(2, pass);  // ❌ raw plain text
        ps.executeUpdate();
    }

    /* ─────────────────────────────────────────────────────
       ✔ RIGHT: BCrypt produces a one-way 60-char hash.
          The original password is never stored or logged.
          Verification uses ENC.matches(input, storedHash).
       ───────────────────────────────────────────────────── */
    public void savePasswordRight(Connection c, String user, String pass)
            throws SQLException {
        if (pass.length() < 10) throw new
            IllegalArgumentException("Password must be ≥ 10 characters.");

        String hash = ENC.encode(pass);
        PreparedStatement ps = c.prepareStatement(
            "INSERT INTO users (username, password_hash) VALUES (?, ?)");
        ps.setString(1, user);
        ps.setString(2, hash);  // ✔ "$2a$12$..." bcrypt string
        ps.executeUpdate();
    }
}

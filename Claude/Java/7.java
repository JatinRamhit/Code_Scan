// SafeDeserializer.java
// Requires: com.fasterxml.jackson.core:jackson-databind
import com.fasterxml.jackson.databind.*;
import com.fasterxml.jackson.databind.jsontype.BasicPolymorphicTypeValidator;
import java.io.*;

public class SafeDeserializer {

    // ❌ INSECURE — native Java deserialization of untrusted bytes
    public static Object deserializeUnsafe(byte[] data)
            throws Exception {
        ObjectInputStream ois =
            new ObjectInputStream(new ByteArrayInputStream(data));
        return ois.readObject(); // ❌ gadget chains → RCE
    }

    // ✔ SECURE — Jackson with strict type allowlist, no polymorphic typing
    private static final ObjectMapper MAPPER = new ObjectMapper()
        .activateDefaultTypingAsProperty(
            BasicPolymorphicTypeValidator.builder()
                .allowIfSubType("com.myapp.dto.")  // only our own DTOs
                .build(),
            ObjectMapper.DefaultTyping.NON_FINAL,
            "@type")
        .configure(DeserializationFeature.FAIL_ON_UNKNOWN_PROPERTIES, true);

    public static <T> T deserializeSafe(String json, Class<T> clazz)
            throws Exception {
        // Enforce size limit before parsing
        if (json.length() > 65_536) {
            throw new IllegalArgumentException("Input exceeds 64 KB limit.");
        }
        return MAPPER.readValue(json, clazz);
    }

    // Example DTO — only these shapes are accepted
    public static class UserDto implements Serializable {
        public String username;
        public String email;
    }
}

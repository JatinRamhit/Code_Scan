import com.fasterxml.jackson.databind.ObjectMapper;

public class DataHandler {
    public UserProfile processInput(String jsonInput) throws IOException {
        // JSON parsing is much safer than Java native serialization
        ObjectMapper mapper = new ObjectMapper();
        return mapper.readValue(jsonInput, UserProfile.class);
    }
}

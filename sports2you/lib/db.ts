import mysql from "mysql2/promise"

// Database connection configuration
const dbConfig = {
  host: "localhost",
  user: "root",
  password: "",
  database: "sports2you",
}

// Create a connection pool
const pool = mysql.createPool(dbConfig)

// Helper function to execute SQL queries
export async function query(sql: string, params: any[] = []) {
  try {
    const [results] = await pool.execute(sql, params)
    return results
  } catch (error) {
    console.error("Database query error:", error)
    throw error
  }
}

// Player-related database functions
export async function getPlayerByUsername(username: string) {
  const players = await query("SELECT * FROM Player WHERE username = ?", [username])
  return players.length > 0 ? players[0] : null
}

export async function getPlayerByEmail(email: string) {
  const players = await query("SELECT * FROM Player WHERE email = ?", [email])
  return players.length > 0 ? players[0] : null
}

export async function createPlayer(playerData: any) {
  const result = await query(
    "INSERT INTO Player (first_name, last_name, age, username, phone_number, password, email, university_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
    [
      playerData.firstName,
      playerData.lastName,
      playerData.age,
      playerData.username,
      playerData.phoneNumber,
      playerData.password, // In a real app, this should be hashed
      playerData.email,
      playerData.universityName,
    ],
  )
  return result
}

// Sport-related database functions
export async function getAllSports() {
  return await query("SELECT * FROM Sport")
}

export async function getSportById(sportId: number) {
  const sports = await query("SELECT * FROM Sport WHERE sport_id = ?", [sportId])
  return sports.length > 0 ? sports[0] : null
}

// Game-related database functions
export async function getAllGames() {
  return await query(`
    SELECT g.*, s.sport_name, p.first_name, p.last_name 
    FROM Game g
    JOIN Sport s ON g.sport_id = s.sport_id
    JOIN Player p ON g.creator_id = p.player_id
    ORDER BY g.game_time
  `)
}

export async function getGameById(gameId: number) {
  const games = await query(
    `
    SELECT g.*, s.sport_name, p.first_name, p.last_name 
    FROM Game g
    JOIN Sport s ON g.sport_id = s.sport_id
    JOIN Player p ON g.creator_id = p.player_id
    WHERE g.game_id = ?
  `,
    [gameId],
  )
  return games.length > 0 ? games[0] : null
}

export async function getGamesByPlayerId(playerId: number) {
  return await query(
    `
    SELECT g.*, s.sport_name
    FROM Game g
    JOIN Sport s ON g.sport_id = s.sport_id
    WHERE g.creator_id = ?
    ORDER BY g.game_time
  `,
    [playerId],
  )
}

export async function createGame(gameData: any) {
  const result = await query(
    "INSERT INTO Game (skill_level_required, location, game_time, creator_id, sport_id) VALUES (?, ?, ?, ?, ?)",
    [gameData.skillLevel, gameData.location, gameData.gameTime, gameData.creatorId, gameData.sportId],
  )
  return result
}

export async function updateGame(gameId: number, gameData: any) {
  const result = await query(
    "UPDATE Game SET skill_level_required = ?, location = ?, game_time = ?, sport_id = ? WHERE game_id = ?",
    [gameData.skillLevel, gameData.location, gameData.gameTime, gameData.sportId, gameId],
  )
  return result
}

export async function deleteGame(gameId: number) {
  const result = await query("DELETE FROM Game WHERE game_id = ?", [gameId])
  return result
}

// Availability-related database functions
export async function getPlayerAvailability(playerId: number) {
  return await query(
    "SELECT * FROM Player_Availability WHERE player_id = ? ORDER BY day_availability, start_availability",
    [playerId],
  )
}

export async function createPlayerAvailability(availabilityData: any) {
  const result = await query(
    "INSERT INTO Player_Availability (player_id, day_availability, start_availability, end_availability) VALUES (?, ?, ?, ?)",
    [
      availabilityData.playerId,
      availabilityData.dayAvailability,
      availabilityData.startAvailability,
      availabilityData.endAvailability,
    ],
  )
  return result
}

export async function updatePlayerAvailability(availabilityId: number, availabilityData: any) {
  const result = await query(
    "UPDATE Player_Availability SET day_availability = ?, start_availability = ?, end_availability = ? WHERE availability_id = ?",
    [
      availabilityData.dayAvailability,
      availabilityData.startAvailability,
      availabilityData.endAvailability,
      availabilityId,
    ],
  )
  return result
}

export async function deletePlayerAvailability(availabilityId: number) {
  const result = await query("DELETE FROM Player_Availability WHERE availability_id = ?", [availabilityId])
  return result
}

// Preferred sports-related database functions
export async function getPlayerPreferredSports(playerId: number) {
  return await query(
    `
    SELECT s.* 
    FROM Preferred p
    JOIN Sport s ON p.sport_id = s.sport_id
    WHERE p.player_id = ?
  `,
    [playerId],
  )
}

export async function addPlayerPreferredSport(playerId: number, sportId: number) {
  const result = await query("INSERT INTO Preferred (player_id, sport_id) VALUES (?, ?)", [playerId, sportId])
  return result
}

export async function removePlayerPreferredSport(playerId: number, sportId: number) {
  const result = await query("DELETE FROM Preferred WHERE player_id = ? AND sport_id = ?", [playerId, sportId])
  return result
}

// Game availability-related database functions
export async function getAvailablePlayersForGame(gameId: number) {
  return await query(
    `
    SELECT p.* 
    FROM All_Available aa
    JOIN Player_Availability pa ON aa.availability_id = pa.availability_id
    JOIN Player p ON pa.player_id = p.player_id
    WHERE aa.game_id = ?
  `,
    [gameId],
  )
}

export async function addPlayerToGame(availabilityId: number, gameId: number) {
  const result = await query("INSERT INTO All_Available (availability_id, game_id) VALUES (?, ?)", [
    availabilityId,
    gameId,
  ])
  return result
}

export async function removePlayerFromGame(availabilityId: number, gameId: number) {
  const result = await query("DELETE FROM All_Available WHERE availability_id = ? AND game_id = ?", [
    availabilityId,
    gameId,
  ])
  return result
}
